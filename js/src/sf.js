(function(window, document) {

'use strict';

if (window.sacfeed) {
	return;
}

var sacfeed = window.sacfeed = {
	'version': 0,
	'urls': {}
};

// vars

var head = document.getElementsByTagName('head')[0];
var date = new Date();

sacfeed.noop = function() {};
sacfeed.scripts = {};
sacfeed.modules = {};
sacfeed.build = date.getFullYear() + '.' + date.getMonth() + '.' + date.getDate() + '.' + date.getHours();
sacfeed.protocol = (window.location.protocol === 'https:') ? 'https:' : 'http:';

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

sacfeed.urls['api'] = sacfeed.protocol + '//api.sacfeed.com/v' + sacfeed.version + '/';
sacfeed.urls['js'] = sacfeed.protocol + '//js.sacfeed.com/v' + sacfeed.version + '/' + (sacfeed.devmode ? 'src/' : sacfeed.build + '/min/');

// delayed

sacfeed.delayed = [];

sacfeed.load = function() {
	sacfeed.delayed.push({
		'type': 'load',
		'arguments': arguments
	});
}; // sacfeed.load

sacfeed.req = function() {
	sacfeed.delayed.push({
		'type': 'req',
		'arguments': arguments
	});
}; // sacfeed.request

// include external script

sacfeed.inc = function(src, callback) {
	callback = callback || sacfeed.noop;

	if (/^\/\//.test(src)) {
		src = sacfeed.protocol + src;
	}

	if (sacfeed.scripts[src]) {
		callback();
		return;
	}

	sacfeed.scripts[src] = true;

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
			callback();
		}, 10);
	};

	script.onload = ready;
	script.onreadystatechange = function() {
		if (this.readyState === 'loaded' || this.readyState === 'complete') {
			ready();
		}
	};

	script.src = src;
	head.appendChild(script);
}; // sacfeed.init

sacfeed.inc(sacfeed.urls['js'] + 'sacfeed.js', function() {
	sacfeed.init();
});

})(window, document);
