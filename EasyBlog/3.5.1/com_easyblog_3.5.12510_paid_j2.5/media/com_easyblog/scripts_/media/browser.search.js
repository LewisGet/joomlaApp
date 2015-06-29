// module: start
EasyBlog.module("media/browser.search", function($){

var module = this;

// require: start
EasyBlog.require()
.library(
	"easing"
)
.done(function(){

// controller: start
EasyBlog.Controller("Media.Browser.Search",

	// Class properties
	{
		defaultOptions: {
			"{searchInput}"  : ".searchInput"
		}
	},

	// Instance properties
	function(self) { return {

		init: function() {

			self.browser.search = self.search;

			self._search = $.debounce(500, self.search);
		},

		search: function(keyword) {

			var keyword = $.trim(keyword).toUpperCase();

			var matchKeyword = function(value) {

				return (value || "").toUpperCase().indexOf(keyword) >= 0;
			}

			var allMatches = [];

			self.browser.item(".item-type-folder").each(function(i, folder) {

				var folder = $(folder).data("item"),

					items = folder.element.find(".browserItem"),

					matches = [];

				$.each(items, function(i, item) {

					var item = $(item).data("item"),

						match = matchKeyword(item.meta.title);

					item.element.toggle(match);

					if (match) {
						matches.push(item);
					}
				});

				if (matches.length < 1 && !matchKeyword(folder.meta.title)) {

					folder.element.hide();
				}

				allMatches = allMatches.concat(matches);
			});

			return allMatches;
		},

		"{searchInput} keyup": function(el) {

			if ($.trim(el.val())=="") {

				self.browser.item().show();

				self.browser.scrollTo(self.browser.currentItem());

				return;
			}

			self._search(el.val());

			self.browser.setItemLayout();
		}
	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end
