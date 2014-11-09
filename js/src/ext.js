(function(sacfeed) {

if (sacfeed.Ext) {
	return;
}

var Ext = sacfeed.Ext = {};

sacfeed.modules['Ext'] = sacfeed.LOADED;
Ext.init = function(callback) {
	delete Ext.init;

	callback = callback || sacfeed.noop;

	sacfeed.modules['Ext'] = sacfeed.INITIALIZED;
	callback();
};

})(window.sacfeed);
