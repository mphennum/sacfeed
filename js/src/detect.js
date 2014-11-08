(function() {

'use strict';

var sacfeed = window.sacfeed;

if (sacfeed.Detect) {
	return;
}

var Detect = sacfeed.Detect = {};

sacfeed.modules['Detect'] = true;
Detect.init = function(callback) {
	delete Detect.init;

	callback = callback || sacfeed.noop;

	Detect.XHR = (XMLHttpRequest && 'withCredentials' in new XMLHttpRequest());
	Detect.XDR = (typeof XDomainRequest !== 'undefined');

	callback();
};

})();
