(function() {

var sacfeed = window.sacfeed;
if (sacfeed && sacfeed.ui) {
	return;
}

var ui = sacfeed.ui = {};

ui.init = function(callback) {
	delete ui.init;

	if (callback) {
		callback();
	}
};

})();
