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
					if (xhr.readyState === 4 && !done) {
						done = true;
						callback(JSON.parse(xhr.responseText));
					}
				};

				xhr.send(null);
			};
		} else {
			sacfeed.request = function(method, url, params, callback) {
				callback = callback || KON.noop;

				var xdr = new XDomainRequest();
				xdr.onprogress = sacfeed.noop; // prevents random "aborted" when the request was successful bug (IE)
				xdr.ontimeout = sacfeed.noop;
				xdr.onerror = sacfeed.noop;
				xdr.onload = function() {
					callback(JSON.parse(xdr.responseText));
				};

				xdr.open(method, url);
				xdr.send();
			};
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
			sacfeed.include(required[i], ready);
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

	sacfeed.request(method, uri, params, callback);
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
	var ready = function() {
		if (++completed < total) {
			return;
		}

		callback();
	};

	for (var i = 0, n = modules.length; i < n; ++i) {
		var module = modules[i];
		if (sacfeed.modules[module]) {
			ready();
			break;
		}

		var pkg = sacfeed.packageMap[module];
		if (pkg) {
			for (var k in pkg) {
				sacfeed.modules[module] = true;
			}

			sacfeed.include(sacfeed.urls['js'] + pkg.toLowerCase().replace('.', '/') + '.js', ready);
		} else {
			sacfeed.modules[module] = true;
			sacfeed.include(sacfeed.urls['js'] + module.toLowerCase().replace('.', '/') + '.js', ready);
		}
	}
};

// initialization

sacfeed.init();

})();
