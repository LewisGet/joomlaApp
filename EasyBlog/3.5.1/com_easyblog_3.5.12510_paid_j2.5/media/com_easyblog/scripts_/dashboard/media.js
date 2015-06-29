// module: start
EasyBlog.module("dashboard/media", function($){

var module = this;

EasyBlog.require()
.done(function(){

// controller: start
EasyBlog.Controller("Dashboard.Media",

	{
		defaultOptions: {
			url: EasyBlog.baseUrl + "&view=media&tmpl=component&e_name=write_content"
		}
	},

	function(self) { return {

		init: function() {

			EasyBlog.dashboard.registerPlugin("media", self);
		},

		items: {},

		addItem: function(meta) {

			// Always add in the beginning of the array
			self.items[meta.place] = [meta].concat(self.items[meta.place]);

			$.each(self.managers, function(i, manager) {

				manager.addItem(meta);
			});
		},

		removeItem: function(meta) {

			var items = self.items[meta.place];

			items = $.grep(items, function(item) {

				return item.path!==meta.path;
			});

			$.each(self.managers, function(i, manager) {

				manager.removeItem(meta);
			});
		},

		managers: [],

		registerManager: function(controller) {

			self.managers.push(controller);
		},

		loadManager: function(callback) {

			self.modal =
				$(document.createElement("iframe"))
					.appendTo("body")
					.css({
						position: "fixed",
						width: "100%",
						height: "100%",
						top: 0,
						left: 0,
						opacity: 0,
						zIndex: 999999
					})
					.one('load', function(){

						self.showManager();

						return callback && callback();
					})
					.attr({
						frameborder: 0,
						allowTransparency: "true",
						src: self.options.url
					});
		},

		showManager: function(callback) {

			if (self.modal == undefined) {

				return self.loadManager(callback);
			}

			self.modal
				.css({
					left: 0
				})
				.animate(
				{
					opacity: 1
				},
				{
					complete: function() {

						if (self.maxi) {
							self.maxi.setLayout();
						}

						callback && callback();
					}
				});
		},

		hideManager: function() {

			self.modal
				.animate({
					opacity: 0
				},
				{
					complete: function() {

						self.modal
							.css({
								left: self.modal.width() * -1
							});
					}
				});
		}
	}}
);

module.resolve();
// controller: end;

});
// require: end;

});
// module: end
