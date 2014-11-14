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
			opts['parent'] = opts['parent'] || 'nav';
			opts['button'] = opts['button'] || '.sf-navbtn';

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

			if (!this.$.children('a').length) {
				sacfeed.req('read', 'section/list', null, (function(resp) {
					if (resp.status.code !== 200 || !resp.result.sections || !resp.result.sections.length) {
						return;
					}

					for (var i = 0, n = resp.result.sections.length; i < n; ++i) {
						var section = resp.result.sections[i];
						this.$.append('<a href="' + sacfeed.urls['www'] + section['id'].replace(/^\//, '') + '">' + section['name'] + '</a>');
					}
				}).bind(this));
			}

			this.$button.click((function(event) {
				this.$.fadeToggle(100);

				if (event.preventDefault) {
					event.preventDefault();
				}

				return false;
			}).bind(this));

			sacfeed.$body.click((function() {
				this.$.fadeOut(100);
			}).bind(this));

			return this;
		}; // Nav.render

		callback();
	}); // sacfeed.load
}; // Nav.init

})(window.sacfeed);
