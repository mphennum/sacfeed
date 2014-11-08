(function() {

var sacfeed = window.sacfeed;
if (sacfeed.Ext) {
	return;
}

var Ext = sacfeed.Ext = {};

Ext.init = function(callback) {
	delete Ext.init;

	callback = callback || sacfeed.noop;

	callback();
};

})();
