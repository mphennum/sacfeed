(function(window, sacfeed, JSON) {

'use strict';

if (sacfeed.modules['sacfeed']) {
	return;
}

// vars

var packages = sacfeed.packages = sacfeed.packages || {};
var packageMap = sacfeed.packageMap = {};
for (var k in packages) {
	var pkg = packages[k];
	for (var i = 0, n = pkg.length; i < n; ++i) {
		packageMap[pkg[i]] = k;
	}
}

sacfeed.urls['js'] = '//js.sacfeed.com/v' +  (sacfeed.devmode ? sacfeed.version + '/src/' : sacfeed.build + '/min/');

// init

sacfeed.modules['sacfeed'] = true;
sacfeed.init = function(callback) {
	delete sacfeed.init;

	callback = callback || sacfeed.noop;

	// preload

	var required = [
		'Poly',
		'Detect'
	];

	if (!window.jQuery) {
		required.unshift('//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js');
	}

	var analyticsMap = {
		'ga': 'Ext.GA'
	};

	var done = 0;
	var total = required.length;
	var ready = function() {
		if (++done < total) {
			return;
		}

		var $ = sacfeed.$ = window.jQuery;
		sacfeed.$body = $('body');
		sacfeed.$header = $('header');
		sacfeed.$main = $('main');

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
				}; // xhr.onreadystatechange

				xhr.send(null);
			}; // sacfeed.request
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

		callback();
	}; // ready

	for (var i = 0; i < total; ++i) {
		var req = required[i];
		if (/^(?:https?:)?\/\//.test(req)) {
			sacfeed.inc(req, ready);
		} else {
			load(req, ready);
		}
	}
};

// api request

var crudMap = {
	'create': 'POST',
	'read': 'GET',
	'update': 'PUT',
	'delete': 'DELETE'
};

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
		responseText = responseText.trim() || '{}';
		var resp = JSON.parse(responseText);
		if (status.code < 200 || status.code > 299) {
			var ttl = status.ttl;
			status = resp;
			status['ttl'] = ttl;
			resp = null;
		}

		callback(status, headers, resp);
	});
}; // sacfeed.req

// random ID

sacfeed.randID = function() {
	return Math.floor(Math.random() * 0x7FFFFFFF).toString(16);
};

// load module(s)

var listen = {};

var namespace = function(modname) {
	if (modname === 'sacfeed') {
		return sacfeed;
	}

	var module = sacfeed;
	var parts = modname.split('.');
	for (var i = 0, n = parts.length; i < n; ++i) {
		module = module[parts[i]];
	}

	return module;
};

var init = function(modname) {
	var ready = function() {
		for (var i = 0, n = listen[modname].length; i < n; ++i) {
			listen[modname][i]();
		}

		delete listen[modname];
	};

	var ns = namespace(modname);
	if (ns.init) {
		ns.init(ready);
		return;
	}

	ready();
};

var load = function(modname, callback) {
	callback = callback || sacfeed.noop;

	if (modname === null) {
		callback();
		return;
	}

	if (modname instanceof Array) {
		var done = 0;
		var n = modname.length;
		var ready = function() {
			if (++done < n) {
				return;
			}

			callback();
		};

		for (var i = 0; i < n; ++i) {
			load(modname[i], ready);
		}

		return;
	}

	if (listen[modname]) {
		listen[modname].push(callback);
	} else if (sacfeed.modules[modname] && !namespace(modname).init) {
		callback();
	} else {
		listen[modname] = [callback];

		var map = packageMap[modname];
		if (map) {
			if (sacfeed.modules[map]) {
				init(modname);
			} else {
				sacfeed.inc(sacfeed.urls['js'] + map.toLowerCase().replace('.', '/') + '.js', function() {
					init(modname);
				});
			}
		} else {
			sacfeed.inc(sacfeed.urls['js'] + modname.toLowerCase().replace('.', '/') + '.js', function() {
				init(modname);
			});
		}
	}
}; // load

})(window, window.sacfeed, window.JSON);
