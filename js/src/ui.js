(function() {

'use strict';

var sacfeed = window.sacfeed;
var UI = sacfeed.UI = sacfeed.UI || {};

sacfeed.modules['UI'] = sacfeed.LOADED;
UI.init = function(callback) {
	delete UI.init;

	callback = callback || sacfeed.noop;

	sacfeed.modules['UI'] = sacfeed.INITIALIZED;
	callback();
}; // UI.init

})();
