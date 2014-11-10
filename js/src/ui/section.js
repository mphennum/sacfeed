(function(sacfeed) {

'use strict';

var UI = sacfeed.UI = sacfeed.UI || {};
if (UI.Section) {
	return;
}

var Section = UI.Section = {};

sacfeed.modules['UI.Section'] = sacfeed.LOADED;
Section.init = function(callback) {
	delete Section.init;

	callback = callback || sacfeed.noop;

	sacfeed.load(['UI.Ele', 'UI.Nav'], function() {
		var Ele = UI.Ele;

		Section = UI.Section = function(opts) {
			if (!(this instanceof Section)) {
				return new Section(opts);
			}

			opts = opts || {};
			opts['parent'] = opts['parent'] || 'body';

			Ele.prototype.constructor.call(this, opts);

			this.nav = new UI.Nav({
				'parent': 'nav',
				'button': '.sf-navbtn'
			}).render();

			return this;
		}; // Section

		Section.prototype = Object.create(Ele.prototype);

		Section.prototype.render = function() {
			Ele.prototype.render.call(this);

			return this;
		};

		callback();
	}); // sacfeed.load
}; // Section.init

})(window.sacfeed);
