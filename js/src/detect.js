(function(sacfeed) {

'use strict';

if (sacfeed.Detect) {
	return;
}

var Detect = sacfeed.Detect = {};

sacfeed.modules['Detect'] = sacfeed.LOADED;
Detect.init = function(callback) {
	delete Detect.init;

	callback = callback || sacfeed.noop;

	Detect.XHR = (XMLHttpRequest && 'withCredentials' in new XMLHttpRequest());
	Detect.XDR = (typeof XDomainRequest !== 'undefined');

	sacfeed.modules['Detect'] = sacfeed.INITIALIZED;
	callback();
}; // Detect.init

})(window.sacfeed);
