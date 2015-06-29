// module: start
EasyBlog.module("media/panel", function($){

var module = this;

// require: start
EasyBlog.require()
.script(
	"media/panel.item"
)
.view(
	"media/panel"
)
.done(function(){

// controller: start
EasyBlog.Controller(

	"Media.Panel",

	{
		defaultOptions: {

			view: {
				panel: "media/panel"
			},

			"{panel}": ".panel"
		}

	},

	function(self) { return {

		init: function() {

			self.setLayout();
		},

		setLayout: function() {

			var panelViewportHeight = self.element.height();

			// Set panel max-height
			// TODO: Using css rule insertion is more ideal.

			if (self.currentPanel) {

				var panelMaxHeight = panelViewportHeight -
									 parseInt(self.currentPanel.element.css("margin-top")) -
									 parseInt(self.currentPanel.element.css("margin-bottom")),

					panelBodyMaxHeight = panelMaxHeight -
					                     parseInt(self.currentPanel.header().height()) -
					                     parseInt(self.currentPanel.footer().height());

				self.currentPanel.element
					.css("max-height", panelMaxHeight);

				self.currentPanel.body()
					.css("max-height", panelBodyMaxHeight);
			}
		},

		getPanelController: function(panelType) {

			return EasyBlog.Controller.Media.Panel[panelType];
		},

		panels: [],

		createPanel: function(item) {

			var panelElement = self.view.panel()
							       .addClass("panel-type-" + item.meta.type)
							       .appendTo(self.element);

			var panelId = $.uid("panel-");

			// Implement controller on item element
			var panel = new EasyBlog.Controller.Media.Panel.Item(

				panelElement,

				{
					controller: {
						id: panelId,
						media: self.media,
						place: self.place,
						panels: self,
						item: item
					}
				}
			);

			self.panels[panelId] = panel;

			return panel;
		},

		removePanel: function(panelId) {

			var panel = self.panels[panelId];

			self.deactivatePanel(

				!(self.currentPanel==panel),

				self.currentPanel,

				function() {

					delete self.panels[panelId];

					if (panel!==undefined) {

						panel.element.remove();
					}
				}
			);
		},

		activatePanel: function(noTransition, panelId, callback) {

			// Reverse arguments
			if (typeof noTransition!=="boolean") {
				callback = panelId;
				panelId = noTransition;
				noTransition = false;
			}

			var panel = self.panels[panelId];

			// If no panel found, just execute callback.
			if (panel===undefined) {

				return callback && callback();
			}

			// If panel to activate is current panel, stop.
			if (self.currentPanel===panel) return;

			// Trigger panel activate method
			panel.activate();

			if (self.currentPanel || noTransition) {

				self.deactivatePanel(

					true,

					self.currentPanel,

					function(){

						self.currentPanel = panel;

						panel.element
							.addClass("active")
							.css({
								left: "0px"
							});

						self.setLayout();

						return callback && callback();
					}
				);

			} else {

				panel.element
					.animate(
					{
						left: "0px"
					},
					{
						duration: 150,

						easing: "easeInCubic",

						complete: function() {

							self.currentPanel = panel;

							panel.element
								.addClass("active");

							self.setLayout();

							return callback && callback();
						}
					});
			}
		},

		deactivatePanel: function(noTransition, panelId, callback) {

			// Reverse arguments
			if (typeof noTransition!=="boolean") {
				callback = panelId;
				panelId = noTransition;
				noTransition = false;
			}

			var panel;

			if (panelId===undefined) {

				panel = self.currentPanel;

			} else {

				// Accepts both panel or panelId
				panel = (typeof panelId==="string") ? self.panels[panelId] : panelId;
			}

			// If no panel found, just execute callback.
			if (panel===undefined) {

				return callback && callback();
			}

			if (self.currentPanel==panel) {

				self.currentPanel = undefined;
			}

			// If panel element not found, just execute callback.
			if (!panel.element) {

				return callback && callback();
			}

			// Trigger panel deactivate method
			panel.deactivate();

			if (noTransition) {

				panel.element
					.removeClass("active")
					.css({
						left: "-400px"
					});

				return callback && callback();

			} else {

				panel.element
					.removeClass("active")
					.animate(
					{
						left: "-400px"
					},
					{
						duration: 150,

						easing: "easeInCubic",

						complete: function() {

							return callback && callback();
						}
					});
			}
		},

		"{panelSectionHeader} click": function(el) {

			el.parent(".panelSection").toggleClass("active");
		}
	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end
