(function() {

'use strict';

var sacfeed = window.sacfeed = window.sacfeed || {};

// vars

sacfeed.packages = sacfeed.packages || {};
sacfeed.packageMap = sacfeed.packageMap || {};
sacfeed.modules = {};
sacfeed.scripts = {};

sacfeed.urls = sacfeed.urls || {};
sacfeed.urls['api'] = '//api.sacfeed.com/';
sacfeed.urls['js'] = '//js.sacfeed.com/' + (sacfeed.devmode ? 'src/' : 'min/');

// local

var JSON = window.JSON;

var crudMap = {
	'create': 'POST',
	'read': 'GET',
	'update': 'PUT',
	'delete': 'DELETE'
};

// init

sacfeed.init = function(callback) {
	delete sacfeed.init;

	callback = callback || sacfeed.noop;

	// preload

	var required = [
		'//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js',
		'Poly',
		'Detect'
	];

	var total = required.length;

	var completed = 0;
	var ready = function() {
		if (++completed < total) {
			return;
		}

		sacfeed.$ = window.jQuery;

		sacfeed.load = load;

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


		for (var i = 0, n = sacfeed.delayed.length; i < n; ++i) {
			var delayed = sacfeed.delayed[i];
			if (delayed.type === 'load') {
				sacfeed.load.apply(sacfeed, delayed.arguments);
			} else if (delayed.type === 'request') {
				sacfeed.request.apply(sacfeed, delayed.arguments);
			}
		}

		delete sacfeed.delayed;

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
		throw new Exception('CRUD action "' + crud + '" not allowed for sacfeed api requests');
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

sacfeed.modules['sacfeed'] = true;
var load = function(modules, callback) {
	callback = callback || sacfeed.noop;

	if (!(modules instanceof Array)) {
		modules = [modules];
	}

	var total = modules.length;
	var completed = 0;
	var ready = function(mods) {
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

		for (var i = 0, n = mods.length; i < n; ++i) {
			var mod = mods[i];
			var parts = mod.split('.');
			mod = sacfeed;
			for (var j = 0, l = parts.length; j < l; ++j) {
				mod = mod[parts[j]];
			}

			mod.init(pkgready);
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
			var pkg = sacfeed.packages[mod];
			for (var k in pkg) {
				mods.push(k);
				sacfeed.modules[k] = true;
			}
		} else {
			mod = module;
			mods = [mod];
			sacfeed.modules[mod] = true;
		}

		// bind not available until poly loads
		sacfeed.inc(sacfeed.urls['js'] + mod.toLowerCase().replace('.', '/') + '.js', (function(mods) {
			return function() {
				ready(mods);
			};
		})(mods));
	}
};

// initialization

sacfeed.init();

})();
