// module: start
EasyBlog.module("media/panel.item", function($) {

var module = this;

// require: start
EasyBlog.require()
.library(
	"dialog",
	"ui/position"
)
.language(
	'COM_EASYBLOG_MM_CONFIRM_DELETE_ITEM',
	'COM_EASYBLOG_MM_CANCEL_BUTTON',
	'COM_EASYBLOG_MM_YES_BUTTON',
	'COM_EASYBLOG_MM_ITEM_DELETE_CONFIRMATION'
)
.done(function() {

// controller: start
EasyBlog.Controller("Media.Panel.Item",

	{
		defaultOptions: {
			view: {
				confirmDelete: "media/delete"
			},
			"{header}": ".panelHeader",
			"{body}": ".panelBody",
			"{footer}": ".panelFooter",
			"{sectionHeader}": ".panelSectionHeader",
			"{sectionContent}": ".panelSectionContent",
			"{removeItemButton}": ".removeItemButton"
		}
	},

	// Instance properties
	function(self) { return {

		init: function() {

			self.element.addClass("busy");

			var PanelHandler = EasyBlog.Controller.Media.Panel[$.String.capitalize(self.item.meta.type)];

			if (PanelHandler===undefined && !self.handlerLoaded) {

				EasyBlog.require()
					.script(
						"media/panel." + self.item.meta.type
					)
					.done(function() {

						// Prevent recursive loop if something went bonkers
						// during the loading of panel handler.
						self.handlerLoaded = true;

						self.init();
					});

				return;
			}

			self.handlerLoaded = true;

			self.handler = new PanelHandler(

				self.element,

				{
					controller: {
						media: self.media,
						place: self.place,
						panel: self,
						item: self.item
					}
				}
			);

			self.element.removeClass("busy");

			self.setLayout();
		},

		setLayout: function() {

			if (self.place.acl().canRemoveItem) {

				if (self.item.meta.path===self.place.browser.options.directorySeparator) return;

				self.removeItemButton()
					.show();
			}

			self.panels.setLayout();
		},

		activate: function() {

			if (self.handler) {
				self.handler.activate && self.handler.activate();
			}
		},

		deactivate: function() {

			if (self.handler) {
				self.handler.deactivate && self.handler.deactivate();
			}
		},

		remove: function() {

			if (self.handler) {

				if (!self.handler._destroyed) {

					self.handler.destroy();
				}
			}

			self.panels.removePanel(self.id);
		},

		"{removeItemButton} click": function() {

			if (!self.place.acl().canRemoveItem)
			{
				return;
			}

			// Ask for confirmation first.
			$.dialog({
				title: $.language('COM_EASYBLOG_MM_CONFIRM_DELETE_ITEM'),
				content: "<div style='font-size: 12px'>" + $.language('COM_EASYBLOG_MM_ITEM_DELETE_CONFIRMATION') + self.item.meta.title + "?</div>",
				showOverlay: false,
	            body: {
	                css: {
	                    minWidth: 200,
	                    minHeight: 100
	                }
	            },
				buttons:
				[
					{
						name: $.language( 'COM_EASYBLOG_MM_CANCEL_BUTTON' ),
						click: function(){
							$.dialog().close();
						}
					},
					{
						name: $.language( 'COM_EASYBLOG_MM_YES_BUTTON' ),
						click: function(){

							// If user confirms to delete the item.
							EasyBlog.ajax(
								"site.views.media.delete",
								{
									place: self.place.id,
									path: self.item.meta.path
								},
								{
									success: function() {

										// Hide the dialog
										$.dialog().close();

										var meta = self.item.meta;

										meta.place = self.place.id;

										self.place.browser.removeItem(self.item);
									},

									fail: function(message) {
										// @TODO: Show message
										console.log( message );
									}
								});
						}
					}
				]
			});

		},

		"{sectionHeader} click": function(sectionHeader) {

			var section = sectionHeader.parent();

			section.toggleClass("active");
		}

	}}

);

// controller: end

module.resolve();

});
// require: end

});
// module: end
