// module: start
EasyBlog.module("dashboard/media.mini", function($) {

var module = this;

EasyBlog.require()
.library(
	"throttle-debounce",
	"ui/position",
	"dialog"
)
.view(
	"dashboard/media.mini",
	"dashboard/media.mini.item"
)
.language(
	'COM_EASYBLOG_MM_CONFIRM_DELETE_ITEM',
	'COM_EASYBLOG_MM_CANCEL_BUTTON',
	'COM_EASYBLOG_MM_YES_BUTTON',
	'COM_EASYBLOG_MM_ITEM_DELETE_CONFIRMATION',
	"COM_EASYBLOG_LOADING",
	"COM_EASYBLOG_DELETING"
)
.done(function(){

// controller: start
EasyBlog.Controller("Dashboard.Media.Mini",

	{
		defaultOptions: {

			view: {
				body: "dashboard/media.mini",
				item: "dashboard/media.mini.item"
			},

			// Places
			places: [],
			uploader: "",
			items: undefined,

			// Image item
			"{itemLoader}"		: ".placeItem .loader",
			"{emptyList}"		: ".nothing",

			"{headerTitle}": ".headerTitle",

			"{uploadButton}": ".uploadButton",
			"{openMediaManagerButton}": ".openMediaManagerButton",
			"{openMediaManagerButtonContainer}": ".openMediaManagerButtonContainer",

			"{header}": ".browserHeader",
			"{content}": ".browserContent",


			"{messageGroup}": ".browserMessageGroup",
			"{message}": ".browserMessage",

			"{itemGroup}": ".browserItemGroup",
			"{item}": ".browserItem",
			"{removeItemButton}": ".removeItemButton"
		}
	},

	// Instance properties
	function(self) { return {

		init: function() {

			EasyBlog.dashboard.media.registerManager(self);

			self.element
				.append(self.view.body({
					showUploadButton: self.options.uploader == "" ? false : true
				}));

			if (self.options.showMediaManagerButton) {

				self.openMediaManagerButtonContainer().show();
			}

			self.setLayout();

			self._bind(
				self.content(),
				"scroll",
				$.debounce(500, self.setItemLayout)
			);

			self.threadLimit = self.options.threadLimit;

			// Start retrieving items for the mini manager.
			self.retrieveItems();

			// Initialize uploader.
			self.initUploader();
		},

		"{window} resize": function() {

			self.setItemLayout();
		},

		initUploader: function(){

			self.uploadButton().css("opacity", 0);

			EasyBlog.require()
				.script(
					"dashboard/media.mini.uploader"
				)
				.done(function()
				{
					self.uploader = new EasyBlog.Controller.Dashboard.Media.Mini.Uploader(
						self.element,
						{
							controller: {
								browser: self
							},
							settings: self.options.uploader
						}
					);

					self.uploadButton().css("opacity", 1);

					try {
						self.uploader.refresh();
					} catch(e) {}

				});
		},

		setLayout: function() {

			var contentHeight = self.element.height() - self.header().outerHeight() - (self.content().outerHeight() - self.content().height());

			self.content().height(contentHeight);

			self.messageGroup().height(contentHeight);

			try {
				self.uploader.refresh();
			} catch(e) {}
		},

		setItemLayout: function() {

			var optimalWidth = 96,

				availableWidth = self.itemGroup().width(),

				availableColumns = Math.floor(availableWidth / optimalWidth),

				finalSize = Math.floor(availableWidth / availableColumns);

			self.item()
				.width(finalSize)
				.height(finalSize)
				.trigger("setLayout");
		},

		setTitle: function(title) {

			var headerTitle = self.headerTitle();

			headerTitle.html(title);
		},

		retrieveItems: function() {

			self.content()
				.addClass("busy");

			var allItems = [],
				places = [];

			// Convert it into a volatile array
			$.each(self.options.places, function(i, place) {
				places.push(place);
			});

			var loadItems = function() {

				var media = EasyBlog.dashboard.media,

					place = places.shift(),

					callback = 	{

						beforeSend: function() {

							self.setTitle($.language("COM_EASYBLOG_LOADING") + " " + place.title + "...");
						},
						success: function(items) {

							// Hack: Add place property to each meta
							$.each(items, function(i, item) {
								item.place = place.id;
							});

							// Store a copy in main media
							// (so secondary instance won't have to load again)
							media.items[place.id] = items;

							allItems = allItems.concat(items);
						},
						complete: function() {

							if (places.length < 1) {

								self.setTitle("");

								self.content()
									.removeClass("busy");

								self.populate(allItems);

							} else {

								// Continue to load items
								loadItems();
							}
						}
					}

				var items = media.items[place.id];

				if (!items) {

					EasyBlog.ajax(
						"site.views.media.listImages",
						{
							place: place.id,
							variation: false
						},
						callback
					);

				} else {

					allItems = allItems.concat(items);

					callback.complete();
				}

			};

			loadItems();
		},

		items: {},

		itemByPath: {},

		populate: function(items) {

			self.itemGroup().empty();

			if (items.length < 1) {

				self.showMessage("noImages");

				return;
			}

			$.each(items, function(i, meta) {

				var item = self.createItem(meta);

				item.element
					.appendTo(self.itemGroup());
			});

			setTimeout(self.setItemLayout, 100);
		},

		createItem: function(meta) {

			var item = {
				id: meta.id || $.uid("item-"),
				meta: meta,
				element: self.view.item({item: meta})
			}

			// Register item in our registry of items
			self.items[item.id] = item;

			self.itemByPath[item.meta.place+item.meta.path] = item;

			// Add item element to item group
			item.element.data("item", item);

			return item;
		},

		addItem: function(meta) {

			var acceptMeta = false;

			$.each(self.options.places, function(i, place) {

				if (place.id===meta.place) {
					acceptMeta = true;
				}
			});

			if (!acceptMeta) return;

			self.hideMessage();

			var item = self.createItem(meta);

			item.element
				.prependTo(self.itemGroup());

			self.setItemLayout();
		},

		removeItem: function(meta) {

			var item = self.itemByPath[meta.place+meta.path];

			if (item===undefined) return;

			item.element.remove();

			delete self.items[item.id];

			delete self.itemByPath[meta.place+meta.path];
		},

		removeItemDialog: function(item) {

			// Ask for confirmation first.
			$.dialog({
				title: $.language('COM_EASYBLOG_MM_CONFIRM_DELETE_ITEM'),
				content: "<div style='font-size: 12px'>" + $.language('COM_EASYBLOG_MM_ITEM_DELETE_CONFIRMATION') + item.meta.title + "?</div>",
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

							EasyBlog.ajax(
								"site.views.media.delete",
								{
									place: item.meta.place,
									path: item.meta.path
								},
								{
									beforeSend: function() {

										self.element.addClass("uploading");

										self.setTitle($.language("COM_EASYBLOG_DELETING") + " " + item.meta.title + "...");

										item.element.find(".itemIcon").css("opacity", 0.3);
									},

									complete: function() {

										self.element.removeClass("uploading");

										self.setTitle("");

										self.removeItem(item.meta);
									},

									fail: function(message) {

										// @TODO: Show message
										console.error( message );
									}
							});

							$.dialog().close();

						}
					}
				]
			});
		},

		"{removeItemButton} click": function(el, event) {

			event.stopPropagation();

			var item = el.parents(".browserItem").data("item");

			if (item===undefined) return;

			self.removeItemDialog(item);
		},

		"{item} mouseenter": function(el, event) {

			if (!el.hasClass("hasIcon")) return;

			var itemIcon = el.find(".itemIcon"),
				removeItemButton = el.find(".removeItemButton");

			removeItemButton
				.css({
					top: parseInt(itemIcon.css("top")) - 8,
					left: parseInt(itemIcon.css("left")) + parseInt(itemIcon.css("width")) - 8,
				})
				.show();
		},

		"{item} mouseleave": function(el, event) {

			el.find(".removeItemButton").hide();
		},

		isVisible: function(itemEl) {

			var browserContent = self.content(),
				itemHeight = itemEl.outerHeight(),
				itemTop = itemEl.offset().top,
				itemBottom = itemTop + itemHeight,
				y1 = browserContent.offset().top,
				y2 = y1 + browserContent.height();

			return !((itemTop < y1 && itemBottom < y1) || (itemTop > y2 && itemBottom > y2));
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
		},

		// Tasks & thread limit

		tasks: [],

		threads: 0,

		threadLimit: 8,

		addTask: function(task) {

			self.tasks.push(task);

			self.runTask();
		},

		runTask: function() {

			if (self.tasks.length > 0) {

				if (self.threads < self.threadLimit) {

					self.threads++;

					var task = self.tasks.shift();

					// Create icon load thread
					var thread = $.Deferred();

					task.apply(thread);

					// When thread is complete
					thread.always(function(){

						// Reduce thread count
						self.threads--;

						// And see if there's anymore task to run
						self.runTask();
					});

					self.runTask();
				}
			}
		},

		"{item} setLayout": function(el, event) {

			var item = el.data("item"),
				itemIcon = el.find(".itemIcon");

			if (!itemIcon.data("loaded")) {

				if (itemIcon.data("loading")) return;

				self.addTask(function(){

					var thread = this;

					if (!self.isVisible(el)) {

						thread.reject();
					}

					var iconUrl = item.meta.icon.url;

					if (!itemIcon.data("useNaturalUrl")) {

						iconUrl = EasyBlog.baseUrl + "&view=media&layout=getIconImage&place=" + encodeURIComponent(item.meta.place) + "&path=" + encodeURIComponent(item.meta.path) + "&format=image&tmpl=component";
					}

					itemIcon
						.load(function() {

							itemIcon.data("loaded", true);

							itemIcon.data("loading", false);

							item.element.addClass("hasIcon");

							el.trigger("setLayout");

							thread.resolve();
						})
						.error(function() {

							itemIcon.data("loaded", false);

							itemIcon.data("loading", false);

							if (!itemIcon.data("triedNaturalUrl")) {

								itemIcon.data("useNaturalUrl", true);

								itemIcon.data("triedNaturalUrl", true);

								el.trigger("setLayout");
							}

							thread.reject();
						})
						.css({
							opacity: 0
						})
						.attr("src", iconUrl);
				});
			}

			var offset = 8,
				areaWidth = el.width(),
				areaHeight = el.height();

			var size = self.resizeWithin(
				itemIcon.width(),
				itemIcon.height(),
				areaWidth - offset,
				areaHeight - offset
			);

			itemIcon
				.css({
					width: size.width,
					height: size.height,
					top: (areaHeight - size.height) / 2,
					left: (areaWidth - size.width) / 2,
					opacity: 1
				});
		},

		"{self} mouseleave": function() {

			if (self.element.hasClass("uploading")) return;

			self.setTitle("");
		},

		"{item} mouseover": function(el) {

			if (self.element.hasClass("uploading")) return;

			var item = $(el).data('item');

			self.setTitle(item.meta.title);
		},

		"{item} dragstart": function(el) {
			return false;
		},

		"{item} click" : function(el) {

			if (!el.hasClass("hasIcon")) return;

			var item = $(el).data('item'),
				itemIcon = $(el).find(".itemIcon"),
				insert = self.options.insert;

			if (insert) {
				if (insert.call(null, item)===false) return;
			}

			var image = $('<img>').attr('src', item.meta.thumbnail.url);

			if (self.options.enforceImageDimension) {

				var size = self.resizeWithin(
					itemIcon.width(),
					itemIcon.height(),
					self.options.enforceImageWidth,
					self.options.enforceImageHeight
				);

				image.attr({
					width: Math.floor(size.width),
					height: Math.floor(size.height)
				});
			}

			if (self.options.useLightbox) {

				image = $("<a>")
						.addClass("easyblog-thumb-preview")
						.attr({
							href: item.meta.url,
							title: item.meta.title
						})
						.html(image);
			}

			EasyBlog.dashboard.editor.insert(image.toHTML());
		},

		"{openMediaManagerButton} click": function(el) {

			var media = EasyBlog.dashboard.media;

			if (media.modal === undefined) {

				el.addClass("busy");

				media.loadManager(function(){

					el.removeClass("busy");
				});

			} else {

				media.showManager();
			}
		},

		showMessage: function(className, showOverlay) {

			if (showOverlay===undefined) {
				showOverlay = false;
			}

			self.content()
				.css({
					overflowY: "hidden"
				})
				[0].scrollTop = 0;

			var messageGroup = self.messageGroup(),
				message = self.message("."+className);

			if (message.length > 0) {

				// Hide all messages first
				self.message().hide();

				messageGroup
					.toggleClass("withOverlay", showOverlay)
					.show();

				message
					.css({
						opacity: 0
					})
					.show()
					.position({
						my: "center center",
						at: "center center",
						of: messageGroup
					})
					.css({
						opacity: 1
					});
			}
		},

		hideMessage: function() {

			self.content()
				.css({
					overflowY: "scroll"
				});

			self.messageGroup()
				.hide();

			self.message()
				.hide();

			self.setItemLayout();
		}
	}}

);

// controller: end

module.resolve();

});
// require: end

});
// module: end
