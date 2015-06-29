// module: start
EasyBlog.module("dashboard/blogimage", function($) {

var module = this;

EasyBlog.require()
.script(
	"dashboard/media",
	"dashboard/media.mini.launcher"
)
.done(function(){

// controller: start
EasyBlog.Controller("Dashboard.BlogImage",

	{
		defaultOptions: {

			// Containers
			"{dock}"		: ".blogImageDock",
			"{placeHolder}"	: ".blogImagePlaceHolder",
			"{imageData}"	: ".blogImagePlaceHolder input[name=image]",
			"{imageHolder}"	: ".imagePlaceHolder",
			"{image}"       : ".blogImage",

			// Actions
			"{selectBlogImageButton}": ".selectBlogImage",
			"{removeBlogImageButton}": ".removeBlogImage",
			"{doneBlogImageButton}": ".doneBlogImage",

			resizeUsing: "resizeToFill"
		}
	},

	// Instance properties
	function(self) { return {

		init: function() {

			self.dock().hide();

			self.selectBlogImageButton()
				.implement(
					EasyBlog.Controller.Dashboard.Media.Mini.Launcher,
					{
						container: self.options["{dock}"],
						manager: $.extend(self.options.manager, {insert: self.insertImage})
					}
				);
		},

		"{selectBlogImageButton} click": function() {

			var section = $("#editor-write_title"),
				showManager = self.dock().css("display")==="none";

			if (showManager) {

				section.addClass("selectingBlogImage");
				self.dock().show();

			} else {

				self.dock().hide();
				section.removeClass("selectingBlogImage");
			}
		},

		"{doneBlogImageButton} click": function() {

			var section = $("#editor-write_title");
			self.dock().hide();
			section.removeClass("selectingBlogImage");
		},

		"{removeBlogImageButton} click" : function(el) {

			self.image().remove();

			self.imageHolder().show();

			self.imageData().val('');

			el.blur();
		},

		insertImage: function(item) {

			var placeHolder = self.placeHolder();

			placeHolder.addClass("busy");

			// Remove existing images from the selected image.
			placeHolder.find("img").remove();

			self.imageHolder().hide();

			// Append the image into the section.
			var image =
				$(new Image())
					.addClass("blogImage")
					.css({
						position: "absolute",
						opacity: 0,
						left: "-9999px",
					})
					.load(function(){

						var size = self[self.options.resizeUsing](
							image.width(),
							image.height(),
							placeHolder.width(),
							placeHolder.height()
						);

						image.css(size)
							.animate({opacity: 1});

						placeHolder.removeClass("busy");
					})
					.appendTo(placeHolder)
					.attr('src', item.meta.thumbnail.url);

			self.imageData().val($.toJSON(item.meta));

			return false;
		},

		resizeWithin: function(sourceWidth, sourceHeight, maxWidth, maxHeight) {

			var targetWidth = sourceWidth,
				targetHeight = sourceHeight;

			var ratio = maxWidth / sourceWidth;

			targetWidth  = sourceWidth  * ratio;
			targetHeight = sourceHeight * ratio;

			if (targetHeight > maxHeight)
			{
				var ratio = maxHeight / sourceHeight;

				targetWidth  = sourceWidth  * ratio;
				targetHeight = sourceHeight * ratio;
			}

			return {
				top: ((maxHeight - targetHeight) / 2) + 15,
				left: ((maxWidth - targetWidth) / 2) + 15,
				width: targetWidth,
				height: targetHeight
			};
		},

		resizeToFill: function(sourceWidth, sourceHeight, maxWidth, maxHeight) {

			var targetWidth = sourceWidth,
				targetHeight = sourceHeight;

			var ratio = maxWidth / sourceWidth;

			targetWidth  = sourceWidth  * ratio;
			targetHeight = sourceHeight * ratio;

			if (targetHeight < maxHeight) {

				var ratio = maxHeight / sourceHeight;

				targetWidth  = sourceWidth  * ratio;
				targetHeight = sourceHeight * ratio;
			}

			return {
				top: (maxHeight - targetHeight) / 2,
				left: (maxWidth - targetWidth) / 2,
				width: targetWidth,
				height: targetHeight
			};
		}
	}}

);

// controller: end

module.resolve();

});
// require: end

});
// module: end
