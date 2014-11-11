(function(sacfeed) {

'use strict';

if (sacfeed.modules['UI.Nav']) {
	return;
}

var UI = sacfeed.UI = sacfeed.UI || {};
var Nav = UI.Nav = {};

sacfeed.modules['UI.Nav'] = true;
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

			this.$button.click((function(event) {
				this.$.toggle();

				if (event.preventDefault) {
					event.preventDefault();
				}

				return false;
			}).bind(this));

			sacfeed.$body.click((function() {
				this.$.hide();
			}).bind(this));

			return this;
		}; // Nav.render

		callback();
	}); // sacfeed.load
}; // Nav.init

})(window.sacfeed);
