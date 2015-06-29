// module: start
EasyBlog.module("media/panel.folder", function($){

var module = this;

// require: start
EasyBlog.require()
.view(
	"media/panel.folder"
)
.done(function() {

// controller: start
EasyBlog.Controller(

	"Media.Panel.Folder",
	{
		defaultOptions: {

			view: {
				content: "media/panel.folder",
			},

			// Insert button
			"{insertItemButton}": ".insertItemButton",
			"{insertItemDetail}": ".insertItemDetail",

			"{header}": ".panelHeader",
			"{body}": ".panelBody",
			"{footer}": ".panelFooter"
		}
	},

	function(self) { return {

		init: function() {

			self.populate();
		},

		populate: function() {

			self.element.empty();

			// Get the total number of photos only in this folder.
			var totalPhotos	= 0;

			$.each(self.item.handler.items, function(i, item) {

				if (item.meta.type==="image") {
					totalPhotos++;
				}
			});

			self.view.content(
				{
					totalPhotos: totalPhotos,
					meta: self.item.meta
				})
				.appendTo(self.element);

			self.panel.setLayout();
		},

		activate: function() {

			self.populate();
		},

		setLayout: function() {

		},

		//
		// Insert gallery
		//
		"{insertItemButton} click": function() {

			var dashboard = self.media.dashboard;

			// No dashboard, no insertion.
			if (!dashboard) {
				return;
			}
			var type 	= self.place.id == 'jomsocial' ? 'album' : 'gallery';

			var galleryString = '[embed=' + type + ']'
							+ '{'
							+ 'file:"' + self.item.meta.path + '",'
							+ 'place:"' + self.place.id + '"'
							+ '}'
							+ '[/embed]';

			dashboard.editor.insert(galleryString);

			self.media.insertRecentActivity(self.item.meta);
		}
	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end
