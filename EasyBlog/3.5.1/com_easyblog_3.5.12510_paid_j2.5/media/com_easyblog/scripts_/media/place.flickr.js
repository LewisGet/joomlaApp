// module: start
EasyBlog.module("media/place.flickr", function($){

var module = this;

// require: start
EasyBlog.require()
.script(
	"media/browser",
	"media/panel"
)
.view(
	"media/browser",
	"media/place.flickr"
)
.done(function(){

// controller: start
EasyBlog.Controller(

	"Media.Place.Flickr",

	{
		defaultOptions: {

			view: {
				body: "media/place.flickr",
				browser: "media/browser"
			},

			acl: {
				canCreateFolder: false,
				canUploadItem: false,
				canRenameItem: false,
				canRemoveItem: false,
				canCreateVariation: false
			},

			"{browserViewport}" : ".placeBody.Flickr .browserViewport",

			"{panelViewport}"   : ".placeBody.Flickr .panelViewport"
		}
	},

	function(self) { return {

		init: function() {

			// Take this opportunity to load dependencies
			// while user is logging into flickr
			self.dependencies =
				EasyBlog.require()
					.script(
						"media/browser"
					);

			// Set this to the global namespace so that the child can access this controller again.
			window.__flickr	= self;

			self._init();
		},

		_init: function() {

			// if (self.options.associated)
			// {
			// 	self.activate();
			// 	return;
			// }

			self.dependencies
				.done(function(){

					// TODO: Display flickr images

				});
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
		},

		onPlaceEnter: function() {

			if (!self.options.associated)
			{
				self.showLoginForm();
			}

			if( !self.activated )
			{
				self.activate();
			}
		},

		showLoginForm: function() {

			if (self.loginForm) return;

			self.loginForm = $(document.createElement("iframe"));

			self.loginForm
				.css({
					width: self.body.width(),
					height: self.body.height()
				})
				.appendTo(self.body);

			// TODO: Replace login form content with ejs template instead.
			self.loginForm
				.attr("src", EasyBlog.baseUrl + "&view=media&layout=flickrLogin&tmpl=component");



			// console.log( self.loginForm.window );
		}

	}}

);
// controller: end

module.resolve();

})
// require: end

});
// module: end
