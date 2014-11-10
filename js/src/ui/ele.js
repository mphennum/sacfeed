(function(sacfeed) {

'use strict';

var UI = sacfeed.UI = sacfeed.UI || {};
if (UI.Ele) {
	return;
}

var Ele = UI.Ele = {};

sacfeed.modules['UI.Ele'] = sacfeed.LOADED;
Ele.init = function(callback) {
	delete Ele.init;

	callback = callback || sacfeed.noop;

	sacfeed.load('UI', function() {
		var $ = sacfeed.$;

		Ele = UI.Ele = function(opts) {
			if (!(this instanceof Ele)) {
				return new Ele(opts);
			}

			opts = opts || {};

			if (!opts['parent']) {
				throw new Error('UI.Ele must have a parent option');
			}

			this.$ = $(opts['parent']);
			this.parent = opts['parent'];

			return this;
		}; // Ele

		Ele.prototype.render = function() {
			return this;
		}; // Ele.render

		sacfeed.modules['UI.Ele'] = sacfeed.INITIALIZED;
		callback();
	}); // sacfeed.load
}; // Ele.init

})(window.sacfeed);
