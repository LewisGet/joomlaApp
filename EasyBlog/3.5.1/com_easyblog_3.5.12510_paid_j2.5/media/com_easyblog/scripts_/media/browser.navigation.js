// module: start
EasyBlog.module("media/browser.navigation", function($){

var module = this;

// require: start
EasyBlog.require()
.view(
	"media/browser.navigation.item",
	"media/browser.navigation.itemgroup",
	"media/browser.navigation.item.create"
)
.done(function(){

// controller: start
EasyBlog.Controller(

	"Media.Browser.Navigation",

	{
		defaultOptions: {

			view: {
				item: "media/browser.navigation.item",
				itemGroup: "media/browser.navigation.itemgroup",
				itemCreate: "media/browser.navigation.item.create"
			},

			path: "",

			"{itemGroup}": ".navigationItemGroup",
			"{item}": ".navigationItem",

			"{createFolderItem}": ".navigationItem.createFolder",
			"{createFolderForm}": ".createFolderForm",
			"{createFolderInput}": ".createFolderInput",
			"{createFolderButton}": ".createFolderButton",
			"{createFolderToggle}": ".createFolderToggle",
			"{cancelCreateFolderButton}": ".cancelCreateFolderButton"
		}
	},

	function(self) { return {

		init: function() {

			self.browser.navigation = self;
		},

		setPathway: function(path) {

			self.element.empty();

			if (path===undefined) return;

			self.currentPath = path;

			var DS = self.browser.options.directorySeparator;

			var folders = path.split(DS).splice(1),
				nestLevel = 8,
				groupUntil = folders.length - ((folders.length % nestLevel) || nestLevel),
				itemGroup;

			// Base folder
			self.view.item({title: self.browser.baseFolder || DS})
				.addClass("base")
				.data("path", DS)
				.appendTo(self.element);

			if (path!==DS) {

				$.each(folders, function(i, folder) {

					var path = DS + folders.slice(0, i + 1).join(DS),
						item = self.view.item({title: folder})
								   .data("path", path);

					if (i >= groupUntil) {

						item.appendTo(self.element);

						return;

					} else {

						if (i % nestLevel == 0) {

							itemGroup = self.view.itemGroup()
											.appendTo(self.element);
						}

						item.appendTo(itemGroup);
					}
				});
			}

			if (self.place.acl().canCreateFolder) {
				self.view.itemCreate()
					.appendTo(self.element);
			}
		},

		"{itemGroup} mouseover": function(el) {

			clearTimeout(el.data("delayCollapse"));
			el.addClass("expand");
		},

		"{itemGroup} mouseout": function(el) {

			el.data("delayCollapse",
				setTimeout(function() {
					el.removeClass("expand");
				}, 1000)
			);
		},

		"{item} click": function(el) {

			if (el.hasClass("createFolder")) {
				return;
			}

			var path = el.data("path"),

				item = self.browser.itemByPath[path];

			self.browser.currentItem(item);

			self.browser.scrollTo(item);
		},

		showFolderCreationForm: function() {

			self.createFolderItem()
				.addClass("edit");

			self.createFolderInput()
				.focus()[0]
				.select();
		},

		hideFolderCreationForm: function() {

			self.createFolderItem()
				.removeClass("edit");
		},

		"{createFolderToggle} click": function(el) {

			self.showFolderCreationForm();
		},

		"{cancelCreateFolderButton} click": function(el) {

			self.hideFolderCreationForm();
		},

		"{createFolderButton} click": function() {

			var folderName = $.trim(self.createFolderInput().val());

			if (folderName=="") return;

			EasyBlog.ajax(
				'site.views.media.createFolder',
				{
					'place'		: self.place.id,
					'path'      : $.uri(self.currentPath).toPath("./" + folderName).toString()
				},
				{
					beforeSend: function() {

						self.createFolderForm()
							.addClass("busy");

						self.createFolderInput()
							.attr("disabled", true);
					},

					success: function(meta) {

						var parentFolder = self.browser.itemByPath[self.currentPath];

						var folder = self.browser.createFolder(meta, parentFolder);

						self.browser.focusItem(folder);

						self.hideFolderCreationForm();
					},

					fail: function(message) {

						self.createFolderForm()
							.addClass("warning");

						setTimeout(function(){
							self.createFolderInput().focus();
						}, 1);
					},

					complete: function() {

						self.createFolderForm()
							.removeClass("busy");

						self.createFolderInput()
							.attr("disabled", false);
					}
				}
			);
		},

		"{createFolderInput} keyup": function(el, event) {

			self.createFolderForm()
				.removeClass("warning");

			if (event.keyCode==13) {

				self.createFolderButton().trigger("click");
			}
		}

	}}

);
// controller: end

module.resolve();

})
// require: end

});
// module: end
