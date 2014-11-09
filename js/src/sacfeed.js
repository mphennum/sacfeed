(function(window, sacfeed, JSON) {

'use strict';

// vars

sacfeed.packages = sacfeed.packages || {};
sacfeed.packageMap = sacfeed.packageMap || {};

sacfeed.urls['js'] = '//js.sacfeed.com/v' +  (sacfeed.devmode ? sacfeed.version + '/src/' : sacfeed.build + '/min/');

// local

var crudMap = {
	'create': 'POST',
	'read': 'GET',
	'update': 'PUT',
	'delete': 'DELETE'
};

// init

sacfeed.modules['sacfeed'] = sacfeed.LOADED;
sacfeed.init = function(callback) {
	delete sacfeed.init;

	callback = callback || sacfeed.noop;

	// preload

	var required = [
		'//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js',
		'Poly',
		'Detect'
	];

	var analyticsMap = {
		'ga': 'Ext.GA'
	};

	var total = required.length;

	var completed = 0;
	var ready = function() {
		if (++completed < total) {
			return;
		}

		sacfeed.$ = window.jQuery;

		// load
		sacfeed.load = load;

		// request
		if (sacfeed.Detect.XHR) {
			sacfeed.request = function(method, url, params, callback) {
				callback = callback || sacfeed.noop;

				var xhr = new XMLHttpRequest();
				xhr.open(method, url, true);

				var done = false;
				xhr.onreadystatechange = function() {
					if (xhr.readyState !== 4 || done) {
						return;
					}

					done = true;

					var headers = {};
					var head = xhr.getAllResponseHeaders().split('\n');
					for (var i = 0, n = head.length; i < n; ++i) {
						var header = head[i].trim();
						if (!header) {
							continue;
						}

						var parts = header.split(/\s*:\s*/);
						headers[parts[0].toLowerCase()] = parts[1];
					}

					var ttl = 0;
					if (headers['cache-control']) {
						var vars = headers['cache-control'].split(/\s*[,;]\s*/);
						for (var i = 0, n = vars.length; i < n; ++i) {
							var parts = vars[i].trim().split(/\s*=\s*/);
							if (parts[0] === 'max-age') {
								ttl = parseInt(parts[1]);
								break;
							}
						}
					} else if (headers['expires']) {
						var now = new Date();
						var expires = new Date(headers['expires']);
						ttl = parseInt((expires.getTime() - now.getTime()) / 1000);
					}

					var status = {
						'code': xhr['status'],
						'message': xhr['statusText'],
						'ttl': (ttl < 0) ? 0 : ttl
					};

					callback(status, headers, xhr.responseText);
				};

				xhr.send(null);
			};
		} else if (sacfeed.Detect.XDR) {
			sacfeed.request = function(method, url, params, callback) {
				callback = callback || sacfeed.noop;

				var xdr = new XDomainRequest();
				xdr.onprogress = sacfeed.noop; // prevents random "aborted" when the request was successful bug (IE)
				xdr.ontimeout = sacfeed.noop;
				xdr.onerror = sacfeed.noop;

				var done = false;
				xdr.onload = function() {
					if (done) {
						return;
					}

					done = true;

					var status = {};
					var headers = {};
					callback(status, headers, xdr.responseText);
				};

				xdr.open(method, url);
				xdr.send();
			};
		} else { // jsonp fallback
			sacfeed.request = sacfeed.noop;
		}

		// delayed
		for (var i = 0, n = sacfeed.delayed.length; i < n; ++i) {
			var delayed = sacfeed.delayed[i];
			if (delayed.type === 'load') {
				sacfeed.load.apply(sacfeed, delayed.arguments);
			} else if (delayed.type === 'request') {
				sacfeed.request.apply(sacfeed, delayed.arguments);
			}
		}

		delete sacfeed.delayed;

		// analytics
		for (var i = 0, n = sacfeed.analytics.length; i < n; ++i) {
			var analytic = analyticsMap[sacfeed.analytics[i]];
			if (!analytic) {
				throw new Error('Invalid analytics item "' + sacfeed.analytics[i] + '"');
			}

			sacfeed.load(analytic);
		}

		delete sacfeed.analytics;

		sacfeed.modules['sacfeed'] = sacfeed.INITIALIZED;
		callback();
	};

	for (var i = 0, n = required.length; i < n; ++i) {
		if (/^(?:https?:)?\/\//.test(required[i])) {
			sacfeed.inc(required[i], ready);
		} else {
			load(required[i], ready);
		}
	}
};

// api request

sacfeed.req = function(crud, req, params, callback) {
	callback = callback || sacfeed.noop;

	var method = crudMap[crud];
	if (method !== 'GET') {
		throw new Error('CRUD action "' + crud + '" not allowed for sacfeed api requests');
	}

	var uri = sacfeed.urls['api'] + req; // + '.json';
	if (method === 'GET' || method === 'DELETE') {
		var query = [];
		for (var k in params) {
			query.push(encodeURIComponent(k) + '=' + encodeURIComponent(params[k]));
		}

		if (query.length) {
			uri += '?' + query.join('&');
		}
	}

	sacfeed.request(method, uri, params, function(status, headers, responseText) {
		var resp = JSON.parse(responseText.trim());
		if (status.code < 200 || status.code > 299) {
			var ttl = status.ttl;
			status = resp;
			status['ttl'] = ttl;
			resp = null;
		}

		callback(status, headers, resp);
	});
};

// load module(s)

var load = function(modules, callback) {
	callback = callback || sacfeed.noop;

	if (!(modules instanceof Array)) {
		modules = [modules];
	}

	var total = modules.length;
	var completed = 0;
	var ready = function(mods) {
		mods = mods || [];

		var pkgtotal = mods.length;
		var pkgcompleted = 0;
		var pkgready = function() {
			if (++pkgcompleted < pkgtotal) {
				return;
			}

			if (++completed < total) {
				return;
			}

			callback();
		};

		if (!mods.length) {
			pkgready();
			return;
		}

		for (var i = 0, n = mods.length; i < n; ++i) {
			var modname = mods[i];
			var parts = modname.split('.');
			var mod = sacfeed;
			if (parts[0] !== 'sacfeed' && parts[0] !== 'sf') {
				for (var j = 0, l = parts.length; j < l; ++j) {
					mod = mod[parts[j]];
				}
			}

			if (!mod) {
				throw new Error('Module "' + modname + '" not found');
			}

			if (mod.init) {
				mod.init(pkgready);
			}
		}
	};

	for (var i = 0, n = modules.length; i < n; ++i) {
		var module = modules[i];
		if (sacfeed.modules[module]) {
			ready();
			break;
		}

		var mods = [];
		var mod = sacfeed.packageMap[module];
		if (mod) {
			if (sacfeed.modules[mod]) {
				ready();
				break;
			}

			var pkg = sacfeed.packages[mod];
			for (var j = 0, l = pkg.length; j < l; ++j) {
				mods.push(pkg[j]);
				sacfeed.modules[pkg[j]] = sacfeed.modules[pkg[j]] || sacfeed.REQUESTED;
			}
		} else {
			mods = [module];
		}

		// bind not available until poly loads
		sacfeed.inc(sacfeed.urls['js'] + mods[0].toLowerCase().replace('.', '/') + '.js', (function(mods) {
			return function() {
				ready(mods);
			};
		})(mods));
	}
};

// initialization

sacfeed.init();

})(window, window.sacfeed, window.JSON);
