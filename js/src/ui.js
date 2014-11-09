(function() {

var sacfeed = window.sacfeed;
if (sacfeed.UI) {
	return;
}

var UI = sacfeed.UI = {};

sacfeed.modules['UI'] = sacfeed.LOADED;
UI.init = function(callback) {
	delete UI.init;

	callback = callback || sacfeed.noop;

	sacfeed.modules['UI'] = sacfeed.INITIALIZED;
	callback();
};

})();
