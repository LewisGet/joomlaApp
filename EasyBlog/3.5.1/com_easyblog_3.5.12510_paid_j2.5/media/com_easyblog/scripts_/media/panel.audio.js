// module: start
EasyBlog.module("media/panel.audio", function($){

var module = this;

// require: start
EasyBlog.require()
.view(
	"media/panel.audio"
)
.done(function() {

// controller: start
EasyBlog.Controller(

	"Media.Panel.Audio",

	{
		defaultOptions: {

			view: {
				content: "media/panel.audio",
			},

			"{header}": ".panelHeader",
			"{body}": ".panelBody",
			"{footer}": ".panelFooter",

			"{playerContainer}": ".playerContainer",

			// Insert button
			"{insertItemButton}": ".insertItemButton",
			"{insertItemDetail}": ".insertItemDetail",

			// Insert options
			"{autoplay}": ".autoplay"
		}
	},

	function(self) { return {

		init: function() {

			self.view.content({meta: self.item.meta})
				.appendTo(self.element);

			var playerId = "player-" + self.item.id,
				playerWidth = self.playerContainer().width(),
				playerHeight = 24;

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
		// Insert audio
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

			var audioString = '[embed=audio]'
							+ '{'
							+ 'file:"' + self.item.meta.path + '",'
							+ 'autostart:"' + autoplay + '",'
							+ 'place:"' + self.place.id + '"'
							+ '}'
							+ '[/embed]';

			dashboard.editor.insert(audioString);

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
