// module: start
EasyBlog.module("media", function($){

var module = this;

// require: start
EasyBlog.require()
.library(
	"throttle-debounce"
)
.stylesheet(
	"media/style"
)
.view(
	"media/place.menu",
	"media/place.body",
	"media/place.user",
	"media/browser",
	"media/place.shared",
	"media/place.jomsocial",
	"media/browser.item",
	"media/panel",
	"media/panel.section",
	"media/browser.navigation.item",
	"media/browser.navigation.itemgroup",
	"media/browser.navigation.item.create",
	"media/browser.uploader",
	"media/browser.uploader.item",
	"media/browser.item.image",
	"media/browser.item.folder",
	"media/browser.treeitem",
	"media/delete",
	"media/recent.item"
)
.script(
	"media/place"
)
.done(function(){

// controller: start
EasyBlog.Controller(

	"Media",

	{
		defaultOptions: {

			view: {
				recentItem: "media/recent.item"
			},

			"{recentActivities}": ".recentActivities",
			"{recentItemGroup}": ".recentItemGroup"
		}
	},

	function(self) { return {

		init: function() {

			// self.stylesheet = $("link[href*='media/style.css']")[0].sheet;

			self.setLayout = $.debounce(250, function(){
				if (self.places) {
					self.places.setLayout();
				}
			});

			var options = $.extend(self.options.place);

			// Implement places
			self.element
				.implement(
					EasyBlog.Controller.Media.Place,
					{
						controller: {
							media: self
						},

						initialPlace: self.options.initialPlace,
						places: self.options.places
					},
					function() {

						self.places = this
					}
				);

			if (self.dashboard) {
				self.dashboard.media.registerManager(self);
				self.dashboard.media.maxi = self;
			}
		},

		addItem: function(meta) {

			var place = self.places.getPlace(meta.place);

			// Skip if place isn't loaded yet
			if (!place) return;

			// Skip if place's browser isn't loaded yet
			if (!place.browser) return;

			place.browser.addItem(meta);
		},

		removeItem: function() {

			// This is temporarily unused in maxi manager.
			return;

			var place = self.places.getPlace(meta.place);

			// Skip if place isn't loaded yet
			if (!place) return;

			// Skip if place's browser isn't loaded yet
			if (!place.browser) return;

			place.browser.removeItemByPath(meta.path);
		},

		showRecentActivities: function(callback) {

			var recentActivities = self.recentActivities();

			var top = ($(window).height() - recentActivities.outerHeight()) / 2,
				left = ($(window).width() - recentActivities.outerWidth()) / 2;

			recentActivities
				.css({
					top: top - 50,
					left: left,
					opacity: 0
				});

			// Slide down popup
			recentActivities.animate(
			{
				top: top,
				opacity: 1
			},
			{
				duration: 300,
				complete: callback
			});
		},

		hideRecentActivities: function() {

			var recentActivities = self.recentActivities();

			recentActivities.animate(
			{
				opacity: 0,
				top: "-=50px"
			},
			{
				duration: 200,
				complete: function() {

					recentActivities.css({
						left: "-9999px"
					});
				}
			});
		},

		insertRecentActivity: function(meta) {

			clearTimeout(self.insertRecentActivity.timer);

			var recentActivities = self.recentActivities(),
				recentItem = self.view.recentItem({meta: meta});

			// Figure out the dimension & position of items first
			recentItem
				.css({
					opacity: 0
				})
				.prependTo(self.recentItemGroup());

			self.showRecentActivities(function(){

				recentItem.animate(
					{
						opacity: 1
					},
					{
						duration: 300,
						complete: function() {

							self.insertRecentActivity.timer =
								setTimeout(self.hideRecentActivities, 1000);
						}
					}
				);
			});
		},

		"click": function(el, event) {

			if ($(event.target).hasClass("panelViewport")) {

				self.dashboard.media.hideManager();
			}
		},

		"{window} keyup": function(el, event) {

		},

		"{window} resize": function() {

			self.setLayout();
		}

	}}

);
// controller: end

module.resolve();

})
// require: end

});
// module: end
