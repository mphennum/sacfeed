(function() {

var sacfeed = window.sacfeed;
var Ext = sacfeed.Ext = sacfeed.Ext || {};
if (Ext.GA) {
	return;
}

var GA = Ext.GA = {};

GA.init = function(callback) {
	delete GA.init;

	callback = callback || sacfeed.noop;

	callback();
};

})();
