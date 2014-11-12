(function(sacfeed) {

'use strict';

if (sacfeed.modules['Ext.GA']) {
	return;
}

var Ext = sacfeed.Ext = sacfeed.Ext || {};
var GA = Ext.GA = {};

sacfeed.modules['Ext.GA'] = true;
GA.init = function(callback) {
	delete GA.init;

	callback = callback || sacfeed.noop;

	sacfeed.load('Ext', function() {
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-48809303-2', 'auto');
		ga('send', 'pageview');

		callback();
	}); // sacfeed.load
}; // GA.init

})(window.sacfeed);
