(function(sacfeed) {

var Ext = sacfeed.Ext = sacfeed.Ext || {};
if (Ext.GA) {
	return;
}

var GA = Ext.GA = {};

sacfeed.modules['Ext.GA'] = sacfeed.LOADED;
GA.init = function(callback) {
	delete GA.init;

	callback = callback || sacfeed.noop;

	sacfeed.load('Ext', function() {
		sacfeed.modules['Ext.GA'] = sacfeed.INITIALIZED;
		callback();
	});
};

})(window.sacfeed);
