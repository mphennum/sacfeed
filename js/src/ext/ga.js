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
		callback();
	}); // sacfeed.load
}; // GA.init

})(window.sacfeed);
