// module: start
EasyBlog.module("media/browser.item.folder", function($) {

var module = this;

// require: start
EasyBlog.require()
.view(
	"media/browser.item.folder"
)
.done(function() {

// controller: start
EasyBlog.Controller("Media.Browser.Item.Folder",

	{
		defaultOptions: {

			view: {
				body: "media/browser.item.folder"
			},

			"{folderItemGroup}": ".folderItemGroup",
			"{folderItem}": ".browserItem",

			"{instructions}": ".instructions",
			"{uploadButton}": ".uploadButton"
		}
	},

	// Instance properties
	function(self) { return {

		init: function() {

			// Clear out initial item template
			self.element.empty();

			// Replace with folder template
			self.view.body({item: self.item})
				.appendTo(self.element);

			if (self.place.acl().canUploadItem) {

				self.instructions().addClass("canUpload");
			}
		},

		setLayout: function() {

			// This is to make sure parent class's setLayout isn't called.
		},

		"{uploadButton} click": function(el, event) {

			event.stopPropagation();

			self.browser.focusItem(self.item);

			var uploader = self.browser.uploader;

			if (uploader) {

				uploader.showPopup();

				uploader.showUploadForm();
			}
		},

		folders: [],

		items: [],

		addItem: function(item) {

			self.items.push(item);

			item.element
				.appendTo(self.folderItemGroup());

			self.element.removeClass("empty");

			var panel = self.item.panel;

			if (!panel) return;

			if (!panel.handler) return;

			panel.handler.populate();
		},

		// TODO: This is unused.
		removeItem: function(item) {

			if (self.folderItem().length < 1) {

				self.element.addClass("empty");
			}
		},

		populateFolderContents: function() {

			var itemInitializations = [],
				folderEmpty = true;

			$.each(self.item.meta.contents, function(i, meta) {

				if (meta.type=="folder") {

					var folder = self.browser.createFolder(meta, self.item);

				} else {

					var item = self.browser.createItem(meta, self.item);

					// Only when handler is loaded can we resolve the item.
					item.handlerInitialization
						.done(function(){
							item.initialization.resolve();
						});

					itemInitializations.push(item.initialization);

					folderEmpty = false;
				}
			});

			if (folderEmpty) {

				self.element.addClass("empty");
			}

			// Only when folder contents are initalized can we resolve its parent folder.
			$.when.apply(null, itemInitializations)
				.done(function() {

					self.item.initialization.resolve();
				});
		}
	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end
