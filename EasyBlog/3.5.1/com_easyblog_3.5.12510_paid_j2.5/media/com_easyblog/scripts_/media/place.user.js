// module: start
EasyBlog.module("media/place.user", function($){

var module = this;

// require: start
EasyBlog.require()
.script(
	"media/browser",
	"media/panel"
)
.view(
	"media/browser",
	"media/place.user"
)
.done(function(){

// controller: start
EasyBlog.Controller("Media.Place.User",

	{
		defaultOptions: {

			view: {
				body: "media/place.user",
				browser: "media/browser"
			},

			acl: {
				canCreateFolder: true,
				canUploadItem: true,
				canRenameItem: true,
				canRemoveItem: true,
				canCreateVariation: true
			},

			"{browserViewport}" : ".placeBody.User .browserViewport",

			"{panelViewport}"   : ".placeBody.User .panelViewport"
		}
	},

	function(self) { return {

		init: function() {

			// Do not do anything until explicitly being activated by user. (See: onPlaceEnter)
		},

		acl: function() {

			return self.options.acl;
		},

		setLayout: function() {

			var placeHeight = self.element.height();

			self.browserViewport()
				.height(placeHeight);

			self.panelViewport()
				.height(placeHeight);

			if (self.browser) {
				self.browser.setLayout();
			}

			if (self.panels) {
				self.panels.setLayout();
			}
		},

		onPlaceEnter: function() {

			if (!self.activated) {

				self.activate();
			}
		},

		onPlaceLeave: function() {

		},

		activate: function() {

			// Create browser & panel viewports
			self.view.body()
				.appendTo(self.body);

			// Create master browser
			var controllerProps = {
				media: self.media,
				place: self
			}

			self.view.browser()
				.appendTo(self.browserViewport())
				.implement(
					EasyBlog.Controller.Media.Browser,
					{
						controller: controllerProps,
						title: self.title,
						uploader: self.options.uploader
					},
					function() {

						self.browser = this;
					}
				);

			// Create master panel
			self.panelViewport()
				.implement(
					EasyBlog.Controller.Media.Panel,
					{
						controller: controllerProps
					},
					function() {

						self.panels = this;
					}
				);

			self.setLayout();

			self.activated = true;
		}
	}}

);
// controller: end

module.resolve();

})
// require: end

});
// module: end
