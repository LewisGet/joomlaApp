// module: start
EasyBlog.module("media/browser.item", function($) {

var module = this;

// controller: start
EasyBlog.Controller("Media.Browser.Item",

	{
		defaultOptions: {

			iconDelay: 500,

			"{itemTitle}": ".itemTitle",
			"{itemIcon}": ".itemIcon",

			hasCustomHandler: ["folder"]
		}
	},

	// Instance properties
	function(self) { return {

		initialization: $.Deferred(),

		handlerInitialization: $.Deferred(),

		init: function() {

			// Store a reference to the controller inside item data
			// and also add a item-type class.
			self.element
				.data("item", self)
				.addClass("item-type-" + self.meta.type);

			// Initial item before handler kicks in
			self.setTitle();

			// Create item handler
			self.createHandler();
		},

		createHandler: function() {

			if ($.inArray(self.meta.type, self.options.hasCustomHandler) < 0) {

				self.handlerInitialization.resolve();
				return;
			}

			var ItemHandler = EasyBlog.Controller.Media.Browser.Item[$.String.capitalize(self.meta.type)];

			if (ItemHandler===undefined) {

				EasyBlog.require()
					.script(
						"media/browser.item." + self.meta.type
					)
					.done(function() {

						self.createHandler();
					});

				return;
			}

			self.handler = new ItemHandler(

				self.element,

				{
					controller: {
						media: self.media,
						place: self.place,
						browser: self.browser,
						item: self
					}
				}
			);

			self.handlerInitialization.resolve();
		},

		activate: function() {

			if (self.panel===undefined) {

				self.panel = self.place.panels.createPanel(self);
			}

			self.place.panels.activatePanel(self.panel.id);
		},

		remove: function() {

			try {

				// Destroy handler
				if (self.handler) {

					if (!self.handler._destroyed) {

						self.handler.destroy();
					}
				}

				if (self.element) {

					self.element.remove();
				}

			} catch(e) {

			}
		},

		isVisible: function() {

			// TODO: Optimize routines

			var itemGroup = self.browser.itemGroup(),
				itemEl = self.element,

				itemHeight = itemEl.outerHeight(),
				itemTop = itemEl.offset().top,
				itemBottom = itemTop + itemHeight,

				y1 = itemGroup.offset().top,
				y2 = y1 + itemGroup.height();

			return !((itemTop < y1 && itemBottom < y1) || (itemTop > y2 && itemBottom > y2));
		},

		setLayout: function(animate) {

			// Nothing to be done for folders
			if (self.meta.type=="folder") return;

			// Call handler's setLayout if exists
			if (self.handler && $.isFunction(self.handler.setLayout)) {

				return self.handler.setLayout();
			}

			self.setIcon();
		},

		setTitle: function(title) {


			if (self.handler && $.isFunction(self.handler.setTitle)) {

				return self.handler.setTitle(title);

			} else {

				self.itemTitle()
					.html(title || self.meta.title);
			}
		},

		setIcon: function() {

			// If icon is loading, skip.
			if (self.setIcon.loading) return;

			// If no icon given or item has been destroyed, skip.
			if (self.meta.icon===undefined || self._destroyed) return;

			// If icon is loaded, reposition icon.
			if (self.setIcon.loaded) {

				self.setIconLayout();

				return;
			}

			self.setIcon.loading = true;

			self.browser.addTask(function(){

				var thread = this,
					itemIcon = self.itemIcon();

				if (!self.isVisible()) {

					self.setIcon.loading = false;

					thread.reject();

				} else {

					var iconUrl = self.meta.icon.url;

					if (!self.setIcon.useNaturalUrl) {

						if (self.browser.place.id!=="jomsocial" && self.browser.place.id!=="flickr" && self.meta.type=="image") {

							iconUrl = EasyBlog.baseUrl + "&view=media&layout=getIconImage&place=" + encodeURIComponent(self.browser.place.id) + "&path=" + encodeURIComponent(self.meta.path) + "&format=image&tmpl=component";
						}
					}

					itemIcon
						.css({
							opacity: 0
						})
						.load(function() {

							self.setIcon.loaded = true;

							self.setIcon.loading = false;

							thread.resolve();

							self.setLayout();
						})
						.error(function() {

							self.setIcon.loaded = false;

							self.setIcon.loading = false;

							thread.reject();

							if (!self.setIcon.triedNaturalUrl) {

								self.setIcon.useNaturalUrl = true;

								self.setIcon.triedNaturalUrl = true;

								self.setIcon();
							}
						})
						.attr("src", iconUrl);
				}
			});
		},

		setIconLayout: function() {

			var viewMode = self.browser.viewMode(),
				itemIcon = self.itemIcon(),
				itemTitle = self.itemTitle();

			switch (viewMode) {

				case "list":

					var areaWidth = parseInt(itemIcon.css("maxWidth")),
						areaHeight = parseInt(itemIcon.css("maxHeight"));

					itemIcon.css({
						top: (areaHeight - itemIcon.height()) / 2,
						left: (areaWidth - itemIcon.width()) / 2,
						opacity: 1
					});

					break;

				case "tile":
					var offset = 24,
						areaWidth = self.element.width(),
						areaHeight = self.element.height() - itemTitle.height();

					var size = self.resizeWithin(
						itemIcon.width(),
						itemIcon.height(),
						areaWidth - offset,
						areaHeight - offset
					);

					itemIcon.css({
						width: size.width,
						height: size.height,
						top: (areaHeight - size.height) / 2,
						left: (areaWidth - size.width) / 2,
						opacity: 1
					});

					break;
			}
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
				width: targetWidth,
				height: targetHeight
			};
		}

	}}

);

// controller: end

module.resolve();

});
// module: end
