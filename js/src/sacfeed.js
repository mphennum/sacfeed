(function(window, sacfeed, JSON) {

'use strict';

if (sacfeed.modules['sacfeed']) {
	return;
}

// vars

var packages = sacfeed.packages = sacfeed.packages || {};
var packagemap = sacfeed.packagemap = {};
for (var k in packages) {
	var pkg = packages[k];
	for (var i = 0, n = pkg.length; i < n; ++i) {
		packagemap[pkg[i]] = k;
	}
}

var crudmap = {
	'create': 'POST',
	'read': 'GET',
	'update': 'PUT',
	'delete': 'DELETE'
};

var methodmap = {};
for (var k in crudmap) {
	methodmap[crudmap[k]] = k;
}

sacfeed.urls['www'] = sacfeed.protocol + '//www.sacfeed.com/';
sacfeed.urls['js'] = sacfeed.protocol + '//js.sacfeed.com/v' +  (sacfeed.devmode ? sacfeed.version + '/src/' : sacfeed.build + '/min/');
sacfeed.urls['img'] = sacfeed.protocol + '//img.sacfeed.com/v' + (sacfeed.devmode ? sacfeed.version : sacfeed.build) + '/';
sacfeed.urls['authorimg'] = sacfeed.urls['img'] + 'author/';

sacfeed.callbacks = {};

// request

var cache = {};

var query = function(params) {
	if (!params || !Object.keys(params).length) {
		return false;
	}

	var query = [];
	for (var k in params) {
		var v = params[k];
		var key = encodeURIComponent(k);
		if (v === true) {
			query.push(key);
		} else {
			var val = encodeURIComponent(v);
			if (v instanceof Array) {
				val = get ? '%5B' + val + '%5D' : '[' + val + ']';
			}

			query.push(key + '=' + val);
		}
	}

	return query.join('&');
};

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

	var analyticsmap = {
		'ga': 'Ext.GA'
	};

	var done = 0;
	var total = required.length;
	var ready = function() {
		if (++done < total) {
			return;
		}

		var $ = sacfeed.$ = window.jQuery;
		sacfeed.$window = $(window);
		sacfeed.$document = $(document);
		sacfeed.$body = $('body');
		sacfeed.$header = $('header');
		sacfeed.$main = $('main');

		// load
		sacfeed.load = load;

		// request
		if (sacfeed.Detect.XHR) {
			sacfeed.req = function(crud, uri, params, callback) {
				callback = callback || sacfeed.noop;

				var method = crudmap[crud];
				var get = (method === 'GET' || method === 'DELETE');
				var url = sacfeed.urls['api'] + uri.replace(/\?.*$/, '').replace(/\.[^\/]*$/, '');

				var post = null;
				params = query(params);
				if (params) {
					if (get) {
						url += '?' + params;
					} else {
						post = params;
					}
				}

				if (method === 'GET' && cache[url]) {
					var dt = new Date();
					if (cache[url]['expires'] > dt.getTime()) {
						callback(cache[url]['resp']);
						return;
					}

					delete cache[url];
				}

				var xhr = new XMLHttpRequest();
				xhr.open(method, url, true);

				var done = false;
				xhr.onreadystatechange = function() {
					if (xhr.readyState !== 4 || done) {
						return;
					}

					done = true;

					// headers

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

					// ttl

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

					ttl = (ttl < 0) ? 0 : ttl;

					// result

					var responseText = xhr.responseText.trim() || '{}';
					var result = JSON.parse(responseText);

					// status

					var status;
					if (xhr['status'] < 200 || xhr['status'] > 299) {
						status = result;
						status['ttl'] = ttl; // more accurate ttl
						result = {};
					} else {
						status = {
							'code': xhr['status'],
							'message': xhr['statusText'],
							'ttl': ttl
						};
					}

					var resp = {'status': status, 'headers': headers, 'result': result};

					if (method === 'GET' && ttl > 0) {
						var dt = new Date();
						cache[url] = {
							'resp': resp,
							'expires': dt.getTime() + ttl * 1000
						};
					}

					callback(resp);
				}; // xhr.onreadystatechange

				xhr.send(post);
			}; // sacfeed.req
		} else if (sacfeed.Detect.XDR) {
			sacfeed.req = function(crud, uri, params, callback) {
				callback = callback || sacfeed.noop;

				var method = crudmap[crud];
				var url = sacfeed.urls['api'] + uri.replace(/\?.*$/, '').replace(/\.[^\/]*$/, '') + '.xdr';

				// xdr can only use GET and POST methods
				if (method === 'DELETE') {
					method = 'GET';
					params = params || {};
					params['m'] = 'd';
				} else if (method === 'PUT') {
					method = 'POST';
					params = params || {};
					params['m'] = 'u';
				}

				var get = (method === 'GET');
				var post = null;
				params = query(params);
				if (params) {
					if (get) {
						url += '?' + params;
					} else {
						post = params;
					}
				}

				if (crud === 'read' && cache[url]) {
					var dt = new Date();
					if (cache[url]['expires'] > dt.getTime()) {
						callback(cache[url]['resp']);
						return;
					}

					delete cache[url];
				}

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

					var responseText = xdr.responseText.trim() || '{}';
					var resp = JSON.parse(responseText);

					var ttl = resp.status.ttl;
					if (crud === 'read' && ttl > 0) {
						var dt = new Date();
						cache[url] = {
							'resp': resp,
							'expires': dt.getTime() + ttl * 1000
						};
					}

					callback(resp);
				};

				xdr.open(method, url);
				xdr.send(post);
			}; // sacfeed.req
		} else { // jsonp fallback
			sacfeed.req = function(crud, uri, params, callback) {
				callback({}, {}, '');
			}; // sacfeed.req
		}

		// delayed
		for (var i = 0, n = sacfeed.delayed.length; i < n; ++i) {
			var delayed = sacfeed.delayed[i];
			if (delayed.type === 'load') {
				sacfeed.load.apply(sacfeed, delayed.arguments);
			} else if (delayed.type === 'req') {
				sacfeed.req.apply(sacfeed, delayed.arguments);
			}
		}

		delete sacfeed.delayed;

		// analytics
		for (var i = 0, n = sacfeed.analytics.length; i < n; ++i) {
			var analytic = analyticsmap[sacfeed.analytics[i]];
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

// random ID

sacfeed.randID = function() {
	return Math.floor(Math.random() * 0x7FFFFFFF).toString(36);
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

		var map = packagemap[modname];
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
