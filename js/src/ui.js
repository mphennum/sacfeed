(function() {

var sacfeed = window.sacfeed;
if (sacfeed.UI) {
	return;
}

var UI = sacfeed.UI = {};

UI.init = function(callback) {
	delete UI.init;

	callback = callback || sacfeed.noop;

	callback();
};

})();
