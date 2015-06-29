// module: start
EasyBlog.module("dashboard/media.mini.launcher", function($) {

var module = this;

// controller: start
EasyBlog.Controller("Dashboard.Media.Mini.Launcher",

	{
		defaultOptions: {
			container: "",
			manager: {}
		}
	},

	// Instance properties
	function(self) { return {

		init: function() {

			if (self.element.hasClass("active")) {

				self.load();
			}
		},

		"click": function(el) {

			// Fixed CSS pressed behaviour
			el.toggleClass("pressed")
			  .blur();

			if (self.loading) return;

			if (!self.manager) {

				return self.load();
			}

			self.manager.setLayout();
		},

		container: function() {

			return $(self.options.container);
		},

		load: function() {

			var container = self.container();

			container.addClass("busy");

			self.loading = true;

			// Load external controller on demand
			EasyBlog.require()
				.script(
					"dashboard/media.mini"
				)
				.done(function($) {

					container
						.removeClass("busy")
						.implement(
							EasyBlog.Controller.Dashboard.Media.Mini,
							self.options.manager,
							function() {
								self.manager = this;
							}
						);

					self.loading = false;
				});
		}

	}}

);

// controller: end

module.resolve();

});
// module: end
