(function() {

'use strict';

if (window.sacfeed) {
	return;
}

var sacfeed = window.sacfeed = {};

// vars

var head = document.getElementsByTagName('head')[0];

sacfeed.noop = function() {};
sacfeed.scripts = {};
sacfeed.modules = {'sf': true};

// dev mode & analytics

sacfeed.analytics = [];
sacfeed.devmode = false;
var scripts = document.getElementsByTagName('script');
for (var i = 0, n = scripts.length; i < n; ++i) {
	var m = scripts[i].src.match(/\/(min|src)\/sf.js(?:#(.*))?$/);
	if (m) {
		sacfeed.devmode = (m[1] === 'src');
		if (m[2]) {
			sacfeed.analytics = m[2].split(',');
		}

		break;
	}
}

// delayed

sacfeed.delayed = [];

sacfeed.load = function() {
	sacfeed.delayed = {
		'type': 'load',
		'arguments': arguments
	};
};

sacfeed.request = function() {
	sacfeed.delayed = {
		'type': 'request',
		'arguments': arguments
	};
};

// include external script

sacfeed.inc = function(src, callback) {
	callback = callback || sacfeed.noop;

	if (/^\/\//.test(src)) {
		src = 'http' + (window.location.protocol === 'https:' ? 's' : '') + ':' + src;
	}

	if (sacfeed.scripts[src]) {
		callback();
		return;
	}

	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.async = 'true';

	var loaded = false;
	var ready = function() {
		if (loaded) {
			return;
		}

		loaded = true;
		setTimeout(function() {
			head.removeChild(script);
		}, 10);

		sacfeed.scripts[src] = true;
		callback();
	};

	script.onload = ready;
	script.onreadystatechange = function() {
		if (this.readyState === 'loaded' || this.readyState === 'complete') {
			ready();
		}
	};

	script.src = src;
	head.appendChild(script);
};

var date = new Date();


sacfeed.inc('//js.sacfeed.com/' + (sacfeed.devmode ? 'src' : 'min') + '/sacfeed.js');

})();
