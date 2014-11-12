(function(sacfeed) {

'use strict';

if (sacfeed.modules['UI.Section']) {
	return;
}

var UI = sacfeed.UI = sacfeed.UI || {};
var Section = UI.Section = {};

sacfeed.modules['UI.Section'] = true;
Section.init = function(callback) {
	delete Section.init;

	callback = callback || sacfeed.noop;

	sacfeed.load(['UI.Ele', 'UI.Nav'], function() {
		var format = 'l, F j - g:i A';

		var $ = sacfeed.$;
		var Ele = UI.Ele;

		Section = UI.Section = function(opts) {
			if (!(this instanceof Section)) {
				return new Section(opts);
			}

			opts = opts || {};
			opts['parent'] = opts['parent'] || sacfeed.$body;

			this.section = opts['section'] || '/';
			this.first = opts['first'] || null;
			this.last = opts['last'] || null;
			this.authormap = opts['authormap'] || {};
			this.titlemap = opts['titlemap'] || {};

			this.queue = [];
			this.$queuebtn = sacfeed.$header.find('.sf-queuebtn');

			Ele.prototype.constructor.call(this, opts);

			this.nav = new UI.Nav();

			return this;
		}; // Section

		Section.prototype = Object.create(Ele.prototype);

		// fetch

		var fetchAfter = function(section, after, callback) {
			var params = {'section': section};
			if (after) {
				params['a'] = after;
			}

			sacfeed.req('read', 'article/list', params, callback || sacfeed.noop);
		};

		var fetchSince = function(section, since, callback) {
			var params = {'section': section};
			if (since) {
				params['s'] = since;
			}

			sacfeed.req('read', 'article/list', params, callback || sacfeed.noop);
		};

		// queue

		var showQueue = function() {
			var n = (this.queue.length > 9) ? '9+' : this.queue.length;
			this.$queuebtn.text(n);
			this.$queuebtn.fadeIn(300);
		};

		var renderQueue = function() {
			this.$queuebtn.fadeOut(100);

			for (var i = this.queue.length - 1; i > -1; --i) {
				renderArticle.call(this, this.queue[i]);
			}

			this.queue = [];
		};

		// render

		Section.prototype.render = function() {
			Ele.prototype.render.call(this);

			this.nav.render();

			this.$queuebtn.click((function() {
				renderQueue.call(this);
			}).bind(this));

			sacfeed.$main.find('[data-ts]').each(function() {
				var $this = $(this);
				var ts = parseInt($this.data('ts'));
				var dt = new Date(ts);
				$this.text(dt.format(format));
				$this.removeAttr('data-ts');
			});

			var params = {'section': this.section};

			if (this.first) {
				params['s'] = this.first;
			}

			setInterval((function() {
				fetchSince(this.section, this.first, (function(status, headers, resp) {
					if (status.code !== 200 || !resp.articles) {
						return;
					}

					if (resp.articles.length) {
						this.first = resp.articles[0]['id'];
					}

					for (var i = resp.articles.length - 1; i > -1; --i) {
						this.queue.unshift(resp.articles[i]);
					}

					showQueue.call(this);
				}).bind(this));
			}).bind(this), 90 * 1000);

			return this;
		};

		var renderArticle = function(article) {
			var dt = new Date(article['ts']);

			var profile = '';
			var author = article['author'].replace(/^By\s+/, '');
			author = author.replace(/^(.*)\s+(the\s+\1)$/i, '$2');
			author = author.trim();

			if (author === '') {
				for (var k in this.titlemap) {
					var primary = this.titlemap[k];
					var regex = new RegExp(k, 'i');
					var m = article['title'].match(regex);
					if (m) {
						var last = primary.replace(/^.*\s([^\s]+)$/, '$1');
						var email = (primary[0] + last).toLowerCase() + '@sacbee.com';
						author = primary + ' ' + email;
						break;
					}
				}
			}

			var hasauthorimg = false;
			var m = author.match(/\s+([^@\s]+@[^@\s]+(?:,\s*[^@\s]+@[^@\s]+)*|the\s*sacramento\s*bee)$/i);
			if (m) {
				author = author.replace(/\s+(?:[^@\s]+@[^@\s]+(?:,\s*[^@\s]+@[^@\s]+)*|the\s*sacramento\s*bee)$/i, '');
				var authorLC = author.toLowerCase();
				if (this.authormap[authorLC]) {
					hasauthorimg = true;
					var file = sacfeed.urls['authorimg'] + this.authormap[authorLC] + '.jpg';
					profile = '<img class="sf-profile" src="' + file + '" alt="' + author + '">';
				} else {
					var first = author.match(/^([^,]+)(?:,|\s+and)\s+/);
					if (first && this.authormap[first[1].toLowerCase()]) {
						hasauthorimg = true;
						var file = sacfeed.urls['authorimg'] + this.authormap[first[1].toLowerCase()] + '.jpg';
						profile = '<img class="sf-profile" src="' + file + '" alt="' + author + '">';
					}
				}

				author = '<p class="sf-byline"><span class="sf-name">' + author + '</span> ' + m[1] + '</p>';
			} else if (author !== '') {
				author = '<p class="sf-byline"><span class="sf-name">' + author.replace(/^the\s+/, 'The ') + '</span></p>';
			}

			var thumb = '';
			if (article['thumb']) {
				thumb = '<p class="sf-thumb"><a href="' + article['url'] + '"><img src="' + article['thumb'] + '" alt="' + article['title'].replace('"', '\'') + '"></a></p>';
			}

			var $article = $(
				'<article style="display: none" data-id="' + article['id'] + '">' +
					'<div class="sf-top">' +
						thumb +
						'<h2><a href="' + article['url'] + '">' + article['title'] + '</a></h2>' +
						'<p class="sf-summary">' + article['summary'].entityEncode() + '</p>' +
						'<p><a href="' + article['url'] + '">read more</a></p>' +
					'</div>' +
					'<div class="sf-bottom' + (hasauthorimg ? ' sf-authorimg' : '') + '">' +
						profile +
						author +
						'<p class="sf-date">' + dt.format(format) + '</p>' +
					'</div>' +
				'</article>'
			);

			sacfeed.$main.prepend($article);
			$article.fadeIn(300);
		};

		callback();
	}); // sacfeed.load
}; // Section.init

})(window.sacfeed);
