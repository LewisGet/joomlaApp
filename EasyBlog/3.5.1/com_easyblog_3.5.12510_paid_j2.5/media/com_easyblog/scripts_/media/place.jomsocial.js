// module: start
EasyBlog.module("media/place.jomsocial", function($){

var module = this;

// require: start
EasyBlog.require()
.script(
	"media/browser",
	"media/panel"
)
.view(
	"media/browser",
	"media/place.jomsocial"
)
.done(function(){

// controller: start
EasyBlog.Controller("Media.Place.Jomsocial",

	{
		defaultOptions: {

			view: {
				body: "media/place.jomsocial",
				browser: "media/browser"
			},

			acl: {
				canCreateFolder: false,
				canUploadItem: false,
				canRenameItem: false,
				canRemoveItem: false,
				canCreateVariation: false
			},

			// @TODO: We probably need a way to tell the media.browser to not show uploader.
			showUploader		: false,

			"{browserViewport}" : ".placeBody.Jomsocial .browserViewport",

			"{panelViewport}"   : ".placeBody.Jomsocial .panelViewport"
		}
	},

	function(self) { return {

		init: function() {

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
						title: self.title
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
