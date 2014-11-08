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

// dev mode & analytics

sacfeed.analytics = [];
sacfeed.devmode = false;
var scripts = document.getElementsByTagName('script');
for (var i = 0, n = scripts.length; i < n; ++i) {
	var script = scripts[i];
	if (/\/(?:min|src)\/sf.js(?:#.*)?$/.test(script.src)) {
		sacfeed.devmode = true;

		var m = script.src.match(/#(.*)$/);
		if (m[1]) {
			sacfeed.analytics = m[1].split(',');
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

sacfeed.include = function(src, callback) {
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
sacfeed.include('//js.sacfeed.com/' + (sacfeed.devmode ? 'src' : 'min') + '/sacfeed.js');

})();
