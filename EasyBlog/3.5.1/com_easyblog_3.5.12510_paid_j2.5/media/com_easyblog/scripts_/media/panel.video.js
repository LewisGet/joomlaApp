// module: start
EasyBlog.module("media/panel.video", function($){

var module = this;

// require: start
EasyBlog.require()
.view(
	"media/panel.video"
)
.done(function() {

// controller: start
EasyBlog.Controller(

	"Media.Panel.Video",
	{
		defaultOptions: {

			view: {
				content: "media/panel.video",
			},

			// Insert button
			"{insertItemButton}": ".insertItemButton",
			"{insertItemDetail}": ".insertItemDetail",

			"{header}": ".panelHeader",
			"{body}": ".panelBody",
			"{footer}": ".panelFooter",

			"{playerContainer}": ".playerContainer",

			// Insert options
			"{insertWidth}" : ".insertWidth",
			"{insertHeight}": ".insertHeight",
			"{autoplay}": ".autoplay"
		}
	},

	function(self) { return {

		init: function() {

			self.view.content({meta: self.item.meta})
				.appendTo(self.element);

			var playerId = "player-" + self.item.id,
				playerWidth = self.playerContainer().width(),
				playerHeight = playerWidth / 16 * 9;

			self.playerContainer()
				.height(playerHeight)
				.addClass("busy");

			self.playerContainer()
				.attr('id', playerId);

			// @task: Load jwplayer.js and initialize the video player.
			EasyBlog.require()
					.script($.rootPath + "/components/com_easyblog/assets/vendors/jwplayer/jwplayer.js")
					.done(function($){

						self.player =
							jwplayer(playerId).setup({
								id: playerId,
								width: playerWidth,
								height: playerHeight,
								file: self.item.meta.url,
								controlbar: "bottom",
								autostart: false,
								backcolor: "#333333",
								frontcolor: "#ffffff",
								modes: [
									{
										type: 'html5'
									},
									{
										type: 'flash',
										src: $.rootPath + "components/com_easyblog/assets/vendors/jwplayer/player.swf"
									},
									{
										type: 'download'
									}
								]
							});
					});
		},

		setLayout: function() {

		},

		deactivate: function() {

			if (self.player) {

				if (self.player.getState()=="PLAYING") {

					self.player.pause();
				}
			}
		},

		//
		// Insert video
		//

		"{insertItemButton} click": function() {

			var dashboard = self.media.dashboard;

			// No dashboard, no insertion.
			if (!dashboard) {
				return;
			}

			var autoplay    = 'false';
			if (self.autoplay().val() == '1') {
				autoplay = 'true';
			}

			var width	= parseInt( self.insertWidth().val() , 10 );
			var height	= parseInt( self.insertHeight().val(), 10 );

			if( isNaN( width )  || width == 0 )
			{
				width   = self.item.meta.width;
			}

			if( isNaN( height ) || height == 0 )
			{
				height   = self.item.meta.height;
			}

			var videoString = '[embed=video]'
							+ '{'
							+ 'file:"' + self.item.meta.path + '",'
							+ 'width:"' + width.toString() + '",'
							+ 'height:"' + height.toString() + '",'
							+ 'autostart:"' + autoplay + '",'
							+ 'place:"' + self.place.id + '"'
							+ '}'
							+ '[/embed]';

			dashboard.editor.insert(videoString);

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
