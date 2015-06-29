// module: start
EasyBlog.module("media/place.back", function($){

var module = this;

// controller: start
EasyBlog.Controller(

	"Media.Place.Back",

	{
		defaultOptions: {

		}
	},

	function(self) { return {

		init: function() {

			// Don't show Back panel if media manager
			// is loaded without a parent dashboard.
			if (!self.media.dashboard) {

				self.menu.hide();
			}
		},

		onPlaceEnter: function(fromPlace) {

			var dashboard = self.media.dashboard;

			// Reactivate previous place
			self.media.places.activate(fromPlace);

			if (dashboard) {
				dashboard.media.hideManager();
			}
		}
	}}

);
// controller: end

module.resolve();

});
// module: end
