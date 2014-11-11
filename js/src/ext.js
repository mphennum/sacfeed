(function(sacfeed) {

'use strict';

if (sacfeed.modules['Ext']) {
	return;
}

var Ext = sacfeed.Ext = sacfeed.Ext || {};

sacfeed.modules['Ext'] = true;
Ext.init = function(callback) {
	delete Ext.init;

	callback = callback || sacfeed.noop;

	callback();
};

})(window.sacfeed);
