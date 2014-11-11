(function(sacfeed) {

'use strict';

if (sacfeed.modules['UI']) {
	return;
}

var UI = sacfeed.UI = sacfeed.UI || {};

sacfeed.modules['UI'] = true;
UI.init = function(callback) {
	delete UI.init;

	callback = callback || sacfeed.noop;

	callback();
}; // UI.init

})(window.sacfeed);
