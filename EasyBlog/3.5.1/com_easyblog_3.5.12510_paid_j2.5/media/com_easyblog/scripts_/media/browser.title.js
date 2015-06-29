// module: start
EasyBlog.module("media/browser.title", function($){

var module = this;

// require: start
EasyBlog.require()
.library(
	"easing"
)
.done(function(){

// controller: start
EasyBlog.Controller("Media.Browser.Title",

	// Class properties
	{
		defaultOptions: {

			"{title}"        : "> .browserHeader .browserTitle",
			"{titleForm}"    : "> .browserHeader .browserTitle .titleForm",
			"{titleText}"    : "> .browserHeader .browserTitle .titleText",
			"{titleInput}"   : "> .browserHeader .browserTitle .titleInput",
			"{renameButton}" : "> .browserHeader .browserTitle .renameButton",
			"{cancelRenameButton}" : "> .browserHeader .browserTitle .cancelRenameButton"
		}
	},

	// Instance properties
	function(self) { return {

		init: function() {

			self.browser.title = self;

			self.setTitle(self.browser.options.title);
		},

		"{self} setLayout": function() {

			if (self.title().hasClass("edit")) {

				self.setTitleFormLayout();

			} else {

				self.setTitleTextLayout();
			}
		},

		setTitleTextLayout: function() {

			var titleText = self.titleText(),
				maxWidth = self.title().width();

			titleText
				.css("width", "auto");

			if (titleText.width() > maxWidth) {
				titleText.width(maxWidth);
			}
		},

		setTitleFormLayout: function() {

			var title = self.title(),
				titleForm = self.titleForm(),
				titleInput = self.titleInput(),
				renameButton = self.renameButton();

			titleForm
				.width(title.width() - (titleForm.outerWidth() - titleForm.width()));

			titleInput
				.width(
					titleForm.width() -
					(titleInput.outerWidth() - titleInput.width()) -
					renameButton.outerWidth(true) - 4 // margin offset
				);
		},

		stripExtension: function(title) {

			var index = title.lastIndexOf(".");

			return (index > 0) ? title.substring(0, index) : title;
		},

		setTitle: function(title) {

			// var title = self.stripExtension(title || self.browser.currentItem().meta.title);

			self.hideTitleEditingForm();

			var title = title || self.browser.currentItem().meta.title;

			// TODO: Prevent title overriding if renaming?
			self.titleText()
				.html(title)
				.css("width", "auto");

			self.setTitleTextLayout();
		},

		showTitleEditingForm: function() {

			self.title()
				.addClass("edit");

			self.titleInput()
				.val(self.stripExtension(self.browser.currentItem().meta.title))
				.select();

			self.setTitleFormLayout();
		},

		hideTitleEditingForm: function() {

			self.title().removeClass("edit");
		},

		renameTitle: function() {

			var item = self.browser.currentItem();

			EasyBlog.ajax(
				'site.views.media.move',
				{
					'place'		: self.place.id,
					'fromPath'	: item.meta.path,
					'toPath'    : $.uri(item.meta.path).toPath("../" + self.titleInput().val()).toString()
				},
				{
					beforeSend: function() {

						self.titleForm()
							.addClass("busy");
					},

					success: function() {

					},

					fail: function() {

					},

					complete: function() {

						self.titleForm()
							.removeClass("busy");
					}
				}
			);
		},

		"{titleText} mouseover": function(el) {

			// Disable renaming
			return;

			if (!self.place.acl().canRenameItem) return;

			var item = self.browser.currentItem();

			// TODO: Do not show rename hover effect if item cannot be renamed
			el.addClass("hover");
		},

		"{titleText} mouseout": function(el) {

			el.removeClass("hover");
		},

		"{titleText} click": function(el) {

			// Disable renaming
			return;

			if (!self.place.acl().canRenameItem) return;

			var item = self.browser.currentItem();

			// TODO: Check if can be renamed or not first
			self.showTitleEditingForm();
		},

		"{cancelRenameButton} click": function() {

			self.hideTitleEditingForm();
		},

		"{renameButton} click": function() {

			self.renameTitle();
		}
	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end
