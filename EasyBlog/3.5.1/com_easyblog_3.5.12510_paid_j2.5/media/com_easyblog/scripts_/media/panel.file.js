// module: start
EasyBlog.module("media/panel.file", function($){

var module = this;

// require: start
EasyBlog.require()
.view(
	"media/panel.file"
)
.done(function() {

// controller: start
EasyBlog.Controller(

	"Media.Panel.File",

	{
		defaultOptions: {

			view: {
				content: "media/panel.file",
			},

			"{header}": ".panelHeader",
			"{body}": ".panelBody",
			"{footer}": ".panelFooter",

			// Preview
			"{filePreviewCaption}" : ".filePreviewCaption",

			// Insert button
			"{insertItemButton}": ".insertItemButton",
			"{insertItemDetail}": ".insertItemDetail",

			// Insert options
			"{insertCaption}"	: ".insertCaption",
			"{insertAs}"		: ".insertAs"
		}
	},

	function(self) { return {

		init: function() {

			self.view.content({meta: self.item.meta})
				.appendTo(self.element);
		},

		setLayout: function() {

		},

		//
		// Insert file
		//

		"{insertItemButton} click": function() {

			var dashboard = self.media.dashboard;

			// No dashboard, no insertion.
			if (!dashboard) {
				return;
			}

			var link = $('<a></a>');
			link.attr('href', self.item.meta.url);

			//default caption.
			link.attr('title', self.item.meta.title);
			link.attr('alt', self.item.meta.title);
			link.text(self.item.meta.title);

			if( self.insertCaption().val() != '' )
			{
				link.attr('title', self.insertCaption().val() );
				link.attr('alt', self.insertCaption().val() );
				link.text( self.insertCaption().val() );
			}

			if (self.insertAs().val() == 'newpage') {
				link.attr('target', '_BLANK');
			}

			dashboard.editor.insert( link.toHTML() );

			self.media.insertRecentActivity(self.item.meta);
		},

		"{insertCaption} keyup" : function( el ){

			self.filePreviewCaption().html( $(el).val() );
		}
	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end
