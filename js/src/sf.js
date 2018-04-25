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

var noop = sacfeed.noop = function() {};
sacfeed.scripts = {};
sacfeed.modules = {};
sacfeed.build = [date.getFullYear(), date.getMonth() + 1, date.getDate(), '.', date.getHours()].join('');
sacfeed.protocol = (window.location.protocol === 'https:') ? 'https:' : 'http:';

// dev mode & analytics

sacfeed.analytics = [];
sacfeed.devmode = false;
var scripts = document.getElementsByTagName('script');
for (var i = 0; i < scripts.length; ++i) {
	var m = scripts[i].src.match(/\/(min|src)\/sf.js(?:#(.*))?$/);
	if (m) {
		sacfeed.devmode = (m[1] === 'src');
		if (m[2]) {
			sacfeed.analytics = m[2].split(',');
		}

		break;
	}
}

sacfeed.urls['api'] = sacfeed.protocol + '//sacfeed-api.mphennum.com/v' + sacfeed.version + '/';
sacfeed.urls['js'] = sacfeed.protocol + '//sacfeed-js.mphennum.com/v' + sacfeed.version + '/' + (sacfeed.devmode ? 'src/' : sacfeed.build + '/min/');

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
	callback = callback || noop;

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

	var ready = function() {
		callback();
		callback = noop;
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
