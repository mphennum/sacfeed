(function(sacfeed) {

'use strict';

if (sacfeed.modules['UI.Ele']) {
	return;
}

var UI = sacfeed.UI = sacfeed.UI || {};
var Ele = UI.Ele = {};

sacfeed.modules['UI.Ele'] = true;
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

		callback();
	}); // sacfeed.load
}; // Ele.init

})(window.sacfeed);
