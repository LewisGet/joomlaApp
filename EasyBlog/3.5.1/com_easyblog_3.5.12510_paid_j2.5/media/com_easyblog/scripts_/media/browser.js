// module: start
EasyBlog.module("media/browser", function($){

var module = this;

// require: start
EasyBlog.require()
.library(
	"easing",
	"scrollTo"
)
.script(
	"media/browser.item",
	"media/browser.item.folder",
	"media/browser.title",
	"media/browser.search",
	"media/browser.navigation"
)
.view(
	"media/browser.item",
	"media/browser.treeitem"
)
.done(function(){

// controller: start
EasyBlog.Controller("Media.Browser",

	// Class properties
	{
		defaultOptions: {

			view: {
				item: "media/browser.item",
				treeItem: "media/browser.treeitem"
			},

			directorySeparator: "/",

			path: "",

			items: undefined,

			title: "",

			viewMode: "tile",

			"{header}"       : ".browserHeader",
			"{content}"      : ".browserContent",
			"{footer}"       : ".browserFooter",

			"{treeToggleButton}": ".browserTreeToggleButton",
			"{tileViewButton}"  : ".browserTileViewButton",
			"{listViewButton}"  : ".browserListViewButton",

			"{treeItemGroup}"   : ".browserTreeItemGroup",
			"{treeItem}"        : ".browserTreeItem",

			"{itemGroup}"    : ".browserItemGroup",
			"{item}"         : ".browserItem",

			"{headerTitle}"      : ".browserTitle",
			"{headerSearch}"     : ".browserSearch",
			"{headerNavigation}" : ".browserNavigation",
			"{headerUpload}"     : ".browserUploadButton",

			"{dashboardButton}": ".browserDashboardButton",

			"{footerStatus}": ".browserStatus",
			"{footerMessage}": ".browserMessage"
		}
	},

	// Instance properties
	function(self) { return {

		init: function() {

			// Temporary fix
			self.threadLimit = self.media.options.threadLimit;
			self.options.directorySeparator = self.media.options.directorySeparator;

			self.baseFolder = self.options.title;

			self.headerNavigation()
				.implement(
					EasyBlog.Controller.Media.Browser.Navigation,
					{
						controller: self.controllerProps(),
						path: self.options.path,
						baseFolder: self.baseFolder
					}
				);

			self.element
				.implement(
					EasyBlog.Controller.Media.Browser.Title,
					{
						controller: self.controllerProps()
					}
				)
				.implement(
					EasyBlog.Controller.Media.Browser.Search,
					{
						controller: self.controllerProps()
					}
				);

			self.initUploader();

			self.viewMode(self.options.viewMode);

			self.setLayout();

			self.populate();

			// Bind item group scroll event
			self._bind(
				self.itemGroup(),
				"scroll",
				$.debounce(250, self["{itemGroup} scroll"])
			);
		},

		controllerProps: function(prop) {

			return $.extend(
			{
				media: self.media,
				place: self.place,
				browser: self

			}, prop || {});
		},

		initUploader: function() {

			if (self.place.acl().canUploadItem) {

				EasyBlog.require()
					.script(
						"media/browser.uploader"
					)
					.done(function()
					{

						self.uploader = new EasyBlog.Controller.Media.Browser.Uploader(
							self.element,
							{
								controller: self.controllerProps(),
								settings: self.options.uploader
							}
						);

						self.setLayout();
					});
			}
		},

		setLayout: function() {

			self.setLayout.seed = $.uid();

			var browserHeight = self.place.browserViewport().height(),

				contentHeight =
					browserHeight -
					self.header().outerHeight() -
					self.footer().outerHeight();

				self.treeItemGroup()
					.height(contentHeight);

				self.itemGroup()
					.height(contentHeight);

				self.headerTitle()
					.width(
						self.header().width() -
						Math.abs((self.headerSearch().position().left + self.headerSearch().outerWidth()) -
						self.headerUpload().position().left) - 4 // margin offset;
					);

			self.setItemLayout();

			self.trigger("setLayout");
		},

		items: {},

		itemByPath: {},

		populate: function(items) {

			self.itemGroup()
				.addClass("busy");

			// If items have been populated, don't do anything.
			if (self.populated) {
				return;
			}

			items = items || self.options.items;

			// If items have not been loaded yet
			if (items===undefined) {

				// Get items from server side
				self.loadItems({
					success: function(items) {

						self.populate(items);
					},
					fail: function() {

						try { console.error(message) } catch(e) {};
					}
				});

				return;
			}

			var initialItem;

			if ($.isArray(items)) {

				$.each(items, function(i, item) {

					if (item.type=="folder") {

						var item = self.createFolder(item);

						if (i==0) initialItem = item;
					}

				});

			} else {

				initialItem = self.createFolder(items);
			}

			self.populated = true;

			initialItem
				.initialization
				.done(function(){

					self.itemGroup()
						.removeClass("busy");

					self.focusItem(initialItem);

					self.setItemLayout();
				});
		},

		loadItems: function(options) {

			var path = self.options.path;

			return EasyBlog.ajax(
				"site.views.media.listItems",
				{
					place: self.place.id,
					path: self.options.path,
					variation: false
				},
				options
			);
		},

		addItem: function(meta) {

			// Don't add if item already exist.
			if (self.itemByPath[meta.path]) return;

			var DS = self.options.directorySeparator,

				folderPath = meta.path.substr(0, meta.path.lastIndexOf(DS)),

				parentFolder = self.itemByPath[folderPath || DS];

			self.createItem(meta, parentFolder);
		},

		createItem: function(meta, parentFolder) {

			var itemId = meta.id || $.uid("item-");

			var item = new EasyBlog.Controller.Media.Browser.Item(

				self.view.item(),
				{
					controller: self.controllerProps({
						id: itemId,
						meta: meta,
						parentFolder: parentFolder
					})
				}
			);

			if (parentFolder && item.meta.type!=="folder") {

				parentFolder.handler.addItem(item);
			}

			// Register item in our registry of items
			self.items[item.id] = item;
			self.itemByPath[item.meta.path] = item;

			return item;
		},

		createFolder: function(meta, parentFolder) {

			var item = self.createItem(meta, parentFolder);

			item.tree = self.createTreeItem(item);

			if (parentFolder) {

				item.tree
					.insertAfter(parentFolder.tree);

				item.element
					.insertAfter(parentFolder.element);

				// TODO: This should be somewhere else.
				parentFolder.handler.folders.push(item);

			} else {

				item.tree
					.appendTo(self.treeItemGroup());

				item.element
					.appendTo(self.itemGroup());
			}

			// When folder item is initialized
			item.handlerInitialization
				.done(function() {

					// Populate folder contents
					item.handler.populateFolderContents();
				});

			return item;
		},

		removeItemByPath: function(path, silentRemove) {

			var item = self.itemByPath(path);

			self.removeItem(item, silentRemove);
		},

		removeItem: function(item, silentRemove) {

			if (typeof item !== "object") {

				item = self.items[item];
			}

			if (!item) return;

			// Remove child contents
			if (item.meta.type=="folder") {

				$.each(item.handler.folders, function(i, folder) {

					self.removeItem(folder, true);
				});

				$.each(item.handler.items, function(i, item) {

					self.removeItem(item, true);
				});
			}

			if (item.meta.type=="image") {

				item.meta.place = self.place.id;

				var dashboard = self.media.dashboard;

				if (dashboard) {

					dashboard.media.removeItem(item.meta);
				}
			}

			if (item.meta.path===self.options.directorySeparator) {
				item.element.addClass("empty");
				return;
			}

			// Remove item tree
			if (item.tree) {

				item.tree
					.slideUp(function() {

						item.tree.remove();
					});
			}

			// Remove item panel
			if (item.panel) {
				item.panel.remove();
			}

			// Remove item
			item.remove();

			// Remove from registry
			delete self.itemByPath[item.meta.path];
			delete self.items[item.id];

			// Revert focus to parent folder
			if (!silentRemove) {

				if (item.parentFolder) {

					self.focusItem(item.parentFolder);
				}
			}
		},

		focusItem: function(item) {

			if (item===undefined) return;

			var item = self.currentItem(item);

			if (item===undefined) return;

			// We just want to focus on the item
			item.element.removeClass("active");

			self.scrollTo(item);
		},

		activateItem: function(item) {

			if (item===undefined) return;

			// Set current item
			self.currentItem(item);

			item.activate();

			self.trigger("itemActivate", [self, item]);
		},

		currentItem: function(item) {

			var currentItem = self.currentItem.item;

			if (currentItem && currentItem._destroyed) {

				currentItem = self.currentItem.item = undefined;
			}

			if (typeof item === "string") {

				item = self.itemByPath[item];
			}

			if (item!==undefined) {

				if (item._destroyed) {

					return currentItem;
				}

				if (currentItem) {

					currentItem.element.removeClass("active focus");

					if (currentItem.meta.type!=="folder") {

						currentItem.parentFolder.element.removeClass("focus");
					}
				}

				var isFolder = item.meta.type=="folder";

				item.element.addClass("active focus");

				if (!isFolder) {

					item.parentFolder.element.addClass("focus");
				}

				self.title.setTitle(item.meta.title);

				self.currentFolder(
					(isFolder) ? item : item.parentFolder
				);

				return self.currentItem.item = item;
			}

			return currentItem;
		},

		currentFolder: function(folder) {

			var currentFolder = self.currentFolder.folder;

			if (currentFolder && currentFolder._destroyed) {

				currentFolder = self.currentFolder.folder = undefined;
			}

			if (folder!==undefined) {

				if (currentFolder) {

					currentFolder.tree.removeClass("active");
				}

				folder.tree.addClass("active");

				self.navigation.setPathway(folder.meta.path);

				self.trigger("folderActivate", [self, folder]);

				return self.currentFolder.folder = folder;
			}

			return currentFolder;
		},

		scrollTo: function(item) {

			if (item===undefined) return;

			self.itemGroup()
				.scrollTo(item.element, {
					duration: 500,
					easing: 'swing',
					offset: {top: self.itemGroup().height() / 2 * -1}
				});
		},

		"{header} mouseover": function(el, event) {

			event.stopPropagation();
			el.addClass("hover");
		},

		"{header} mouseout": function(el, event) {

			event.stopPropagation();
			el.removeClass("hover");
		},

		"{item} click": function(el, event) {

			// Prevents click event from being bubbled back to parent folder item
			event.stopPropagation();

			var item = el.data("item");

			// Quick hack: Do not show folder panel on flickr.
			if (item.meta.type=="folder" && self.place.id=="flickr") return;

			item.setLayout();

			self.activateItem(item);
		},

		"{self} itemActivate": function(el, event, browser, item) {

			self.title.setTitle(item.meta.title);
		},

		createTreeItem: function(item) {

			var treeItem =
				self.view.treeItem()
					.data("item", item);

			treeItem
				.find(".treeItemTitle")
				.html(item.meta.title);

			return treeItem;
		},

		"{treeItem} click": function(el, event) {

			var item = el.data("item");

			self.focusItem(item);
		},

		"{treeToggleButton} click": function(el, event) {

			el.toggleClass("active");

			self.content()
				.toggleClass("showBrowserTree");

			setTimeout(self.setLayout, 500);
		},

		"{tileViewButton} click": function(el, event) {

			el.addClass("active")
				.siblings()
				.removeClass("active");

			self.viewMode("tile");
		},

		"{listViewButton} click": function(el, event) {

			el.addClass("active")
				.siblings()
				.removeClass("active");

			self.viewMode("list");
		},

		"{itemGroup} scroll": function(el, event) {

			self.setItemLayout();
		},

		"{dashboardButton} click": function(el, event) {

			self.media.dashboard.media.hideManager();
		},

		viewMode: function(mode) {

			var currentMode = self.viewMode.mode;

			if (!currentMode) {

				currentMode = self.viewMode.mode = self.options.viewMode;
			}

			if (mode!==undefined) {

				var itemGroup = self.itemGroup();

				itemGroup
					.removeClass("view-" + currentMode)
					.addClass("view-" + mode);

				self.viewMode.mode = currentMode = mode;

				self.setLayout();

				var currentItem = self.currentItem();

				if (currentItem!==undefined) {

					self.scrollTo(currentItem);
				}
			}

			return currentMode;
		},

		setItemStyle: function() {

			var seed = self.setLayout.seed;

			if (self.setItemStyle.seed===seed) return;

			self.setItemStyle.seed = seed;

			var viewMode = self.viewMode(),
				itemWidth,
				itemHeight,
				rules = {};

			var test = $("<div>").prependTo(self.itemGroup());
			var availableWidth = test.width();
				test.remove();

			switch (viewMode) {

				case "tile":

					var optimalWidth = 128, // TODO: Convert optimalWidth to a slider-based value
						tilesPerRow = Math.floor(availableWidth / optimalWidth);

					itemWidth = Math.floor(availableWidth / tilesPerRow);
					itemHeight = itemWidth; // TODO: Make this configurable

					itemTitleWidth = itemWidth - 24,
					itemTitleLeft = (itemWidth - itemTitleWidth) / 2;

					rules["#MediaManager .browser .browserItemGroup.view-tile .browserItem"] = "width: " + itemWidth + "px; height: " + itemHeight + "px;";
					rules["html #MediaManager .browser .browserItemGroup.view-tile .browserItem.item-type-folder"] = "width: auto !important; height: auto !important;";
					rules["#MediaManager .browser .browserItemGroup.view-tile .browserItem .itemTitle"] = "width: " + itemTitleWidth + "px; left: " + itemTitleLeft + "px;";
					rules["html #MediaManager .browser .browserItemGroup.view-tile .browserItem.item-type-folder .folderHeader .itemTitle"] = "width: auto !important; height: auto !important;";
					break;

				case "list":
					itemWidth = "auto";
					itemHeight = "auto";

					rules["#MediaManager .browser .browserItemGroup.view-list .browserItem"] = "width: " + itemWidth + "px; height: " + itemHeight + "px;";
					rules["html #MediaManager .browser .browserItemGroup.view-list .browserItem.item-type-folder"] =  "width: auto !important; height: auto !important;";
					break;
			}

			var cssRules = "";
			$.each(rules, function(selector, properties) {

				cssRules += selector + "{" + properties + "}\n"
			});

			$(document).ready(function() {

				var head = document.getElementsByTagName("head")[0];

				if (self.itemStyle) {
					try {
						head.removeChild(self.itemStyle);
					} catch(e) {};
				}

				self.itemStyle = document.createElement("style");
				self.itemStyle.type = "text/css";

				if (self.itemStyle.styleSheet) {
					self.itemStyle.styleSheet.cssText = cssRules;
				} else {
					self.itemStyle.appendChild(document.createTextNode(cssRules));
				}

				head.appendChild(self.itemStyle);
			});
		},

		setItemLayout: function() {

			self.setItemStyle();

			var items = self.item(":not(.item-type-folder)").filter(":visible");

			if (items.length < 1) return;

			// Drill down
			var itemGroupOffset = self.itemGroup().offset(),
				item,
				itemOffset,
				j = items.length,
				i = 1;

			if (items.length < 1) return;

			while (Math.abs(j - i) > 1) {

				item = items.eq(i-1);
				itemOffset = item.offset();

				var itemBottom = itemOffset.top - itemGroupOffset.top + item.outerHeight();

				if (itemBottom < 0) {

					i = Math.ceil((j + i) / 2);

				} else {

					j = i;
					i = Math.ceil(j / 2);
				}
			}

			// Locate first item of the row
			var refOffsetTop = items.eq(i-1).offset().top;

			while (true) {

				if (i < 0) break;

				if (items.eq(i-1).offset().top != refOffsetTop) break;

				i--;
			}

			// Binary search divides by 2,
			// so the tolerance has to be more than 2.
			var itemTolerance = 3;

			// Readjust index based on tolerance level
			i = i - itemTolerance;
			if (i < 0) i = 0;

			// Set visible item icon
			var j = 0,
				stopOnInvisibleItem = false;

			while (true) {

				if (i > items.length - 1) break;

				var item = items.eq(i).controller(),
					itemVisible = item.isVisible();

				if (j > itemTolerance && !itemVisible && stopOnInvisibleItem) break;

				item.setLayout();

				// If the current item visible, that's where we start the setLayout chain.
				if (itemVisible) {
					stopOnInvisibleItem = true;
				}

				i++; j++;
			}
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
		}
	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end
