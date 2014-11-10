(function(sacfeed) {

'use strict';

var UI = sacfeed.UI = sacfeed.UI || {};
if (UI.Nav) {
	return;
}

var Nav = UI.Nav = {};

sacfeed.modules['UI.Nav'] = sacfeed.LOADED;
Nav.init = function(callback) {
	delete Nav.init;

	callback = callback || sacfeed.noop;

	sacfeed.load('UI.Ele', function() {
		var $ = sacfeed.$;
		var Ele = UI.Ele;

		Nav = UI.Nav = function(opts) {
			if (!(this instanceof Nav)) {
				return new Nav(opts);
			}

			opts = opts || {};

			Ele.prototype.constructor.call(this, opts);

			if (!opts['button']) {
				throw new Error('UI.Nav must have a button option');
			}

			this.$button = $(opts['button']);

			return this;
		}; // Nav

		Nav.prototype = Object.create(Ele.prototype);

		Nav.prototype.render = function() {
			Ele.prototype.render.call(this);

			this.$button.click((function() {
				this.$.toggle();
			}).bind(this));

			return this;
		}; // Nav.render

		sacfeed.modules['UI.Nav'] = sacfeed.INITIALIZED;
		callback();
	}); // sacfeed.load
}; // Nav.init

})(window.sacfeed);
