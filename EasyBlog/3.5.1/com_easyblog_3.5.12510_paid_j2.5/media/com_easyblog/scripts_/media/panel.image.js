// module: start
EasyBlog.module("media/panel.image", function($){

var module = this;

// require: start
EasyBlog.require()
.library(
	"ui/position"
)
.view(
	"media/panel.image",
	"media/panel.image.variation"
)
.done(function() {

// controller: start
EasyBlog.Controller(

	"Media.Panel.Image",
	{
		defaultOptions: {

			view: {
				content: "media/panel.image",
				variation: "media/panel.image.variation"
			},

			defaultVariation: "thumbnail",
			defaultImageZoomVariation: "original",

			"{header}" : ".panelHeader",
			"{body}"   : ".panelBody",
			"{footer}" : ".panelFooter",

			// Footer
			"{footerText}": ".footerText",

			// Insert button
			"{insertItemButton}": ".insertItemButton",
			"{insertItemDetail}": ".insertItemDetail",

			// Image preview
			"{imagePreview}": ".imagePreview",
			"{imageZoomButton}": ".imageZoomButton",

			// Variation list
			"{imageVariationList}" : ".imageVariationList",
			"{imageVariations}"    : ".imageVariations",
			"{imageVariation}"     : ".imageVariation",

			// Variation form
			"{imageVariationForm}"    : ".imageVariationForm",
			"{addVariationButton}"    : ".addVariationButton",
			"{backVariationButton}"   : ".backVariationButton",
			"{createVariationButton}" : ".createVariationButton",
			"{removeVariationButton}" : ".removeVariationButton",
			"{newVariationName}"      : ".newVariationName",
			"{newVariationWidth}"     : ".newVariationWidth",
			"{newVariationHeight}"    : ".newVariationHeight",
			"{newVariationRatio}"     : ".newVariationRatio",
			"{imageVariationMessage}" : ".imageVariationMessage",
			variationNameFilter       : new RegExp('[^a-zA-Z0-9]','g'),

			// Insert options
			"{imageZoomOption}": ".imageZoomOption",
			"{imageZoomLargeImageSelection}": ".imageZoomLargeImageSelection",
			"{imageZoomLargeImageOption}": ".imageZoomLargeImageSelection option",
			"{imageCaptionOption}": ".imageCaptionOption",
			"{imageCaption}": ".imageCaption",
			"{imageEnforceDimensionOption}": ".imageEnforceDimensionOption",
			"{imageEnforceWidth}": ".imageEnforceWidth",
			"{imageEnforceHeight}": ".imageEnforceHeight",

			// File properties
			"{itemFilesize}": ".itemFilesize",
			"{itemMime}": ".itemMime",
			"{itemCreationDate}": ".itemCreationDate",
			"{itemUrl}": ".itemUrl"
		}
	},

	function(self) { return {

		init: function() {

			var panelContent = self.view.content({
				meta: self.item.meta
			});

			self.element
				.append(panelContent);

			self.setLayout();

			self.previewImage();

			self.loadVariations();

			var imageCaptionOption = self.imageCaptionOption();

			self._bind(imageCaptionOption, "change", function(event) {

				imageCaptionOption.parent(".field").toggleClass("hide-field-content", !imageCaptionOption.is(":checked"));
			});

			var imageEnforceDimensionOption = self.imageEnforceDimensionOption();

			self._bind(imageEnforceDimensionOption, "change", (function(event) {

				imageEnforceDimensionOption.parent(".field").toggleClass("hide-field-content", !imageEnforceDimensionOption.is(":checked"));

				self.imageEnforceWidth().trigger("keyup");

				return arguments.callee;
			})());

		},

		setLayout: function() {

			if (self.place.acl().canCreateVariation) {

				self.addVariationButton()
					.show();
			}
		},

		"{insertItemButton} click": function() {

			var dashboard = self.media.dashboard,
				variation = self.currentVariation();

			// No dashboard, no insertion.
			if (!dashboard) {
				return;
			}

			var image = $(new Image());

			image.attr({
				src: variation.url,
				alt: variation.title,
				width: variation.width,
				height: variation.height,
				title: self.item.meta.title
			});

			// Enable image caption
			var imageCaption;
			if (self.imageCaptionOption().is(":checked")) {

				imageCaption = self.imageCaption().val();

				image
					.addClass("easyblog-image-caption")
					.attr({
						title: imageCaption || self.item.meta.title
					});
			}

			// Enforce image dimension
			if (self.imageEnforceDimensionOption().is(":checked")) {

				self.imageEnforceWidth().trigger("keyup");

				image.attr({
					width: self.imageEnforceWidth().val(),
					height: self.imageEnforceHeight().val()
				});
			}

			// Enable image zooming
			if (self.imageZoomOption().is(":checked")) {

				var largeImageVariation =
					self.imageZoomLargeImageOption(":selected").data("variation");

				image = $("<a>")
					.addClass("easyblog-thumb-preview")
					.attr({
						href: largeImageVariation.url,
						title: imageCaption || self.item.meta.title
					})
					.html(image);
			}

			dashboard.editor.insert(image.toHTML());

			self.media.insertRecentActivity(self.item.meta);
		},

		//
		// Variation list
		//

		currentVariation: function(variation) {

			if ($.isPlainObject(variation)) {

				self.currentVariation.variation = variation;

				self.insertItemDetail()
					.html(

						$.String.capitalize(variation.name) +

						(($.isNumeric(variation.width) && $.isNumeric(variation.height)) ?

							" " + variation.width + "x" + variation.height :

							"")
					);

				self.footerText()
					.html(variation.filesize);

				self.itemFilesize()
					.html(variation.filesize);

				self.itemMime()
					.html(variation.mime);

				self.itemCreationDate()
					.html(variation.creationDate);

				self.itemUrl()
					.html(variation.url);
			}

			return self.currentVariation.variation;
		},

		loadVariations: function() {

			EasyBlog.ajax(
				"site.views.media.listItems",
				{
					place: self.place.id,
					path: self.item.meta.path,
					variation: '1'
				},
				{
					beforeSend: function() {

						self.imageVariations()
							.empty()
							.addClass("busy");
					},

					success: function(metaWithVariations) {

						$.extend(self.item.meta, metaWithVariations);

						self.populateImageVariations();
					},

					error: function() {

						// Show "There was an error retrieving variations, try again."

						if (self.loadVariations.count===undefined) {

							self.loadVariations.count = 0;
						}

						if (self.loadVariations.count < 3) {

							self.loadVariations.count++;

							self.loadVariations();
						}
					},

					complete: function() {

						self.imageVariations()
							.removeClass("busy");

						var imageZoomOption = self.imageZoomOption();

						self._bind(imageZoomOption, "change", (function(event) {

							imageZoomOption.parent(".field").toggleClass("hide-field-content", !imageZoomOption.is(":checked"));

							return arguments.callee;
						})());
					}
				}
			);
		},

		addVariation: function(variation) {

			if ($.isObject(variation)) {
				self.item.meta.variations.push(variation);
			}
		},

		addImageVariation: function(variation) {

			var imageVariation = self.view.variation({variation: variation});

			imageVariation
				.data("variation", variation)
				.appendTo(self.imageVariations());

			if (!variation.canDelete) {
				imageVariation.addClass("locked");
			}

			// Also add to insert options
			var variationName = $.String.capitalize(variation.name),
				largeImageOption =  $("<option>")
										.val(variationName)
										.html(variationName)
										.data("variation", variation);

			if (variation.name==self.options.defaultImageZoomVariation) {
				largeImageOption.attr("selected", true);
			}

			self.imageZoomLargeImageSelection()
				.append(largeImageOption);

			return imageVariation;
		},

		populateImageVariations: function() {

			var hasDefaultVariation = false;

			$.each(self.item.meta.variations, function(i, variation) {

				if (variation.name=="icon") return;

				var imageVariation = self.addImageVariation(variation);

				// Automatically highlight default item
				if (variation["default"]!==undefined) {

					imageVariation
						.addClass("default")
						.click();

					hasDefaultVariation = true;
				}
			});

			if (!hasDefaultVariation) {
				self.imageVariation(":first")
					.click();
			}
		},

		"{imageVariation} click" : function(el) {

			var variation = el.data("variation");

			self.imageVariation()
				.removeClass("active");

			el.addClass("active");

			self.removeVariationButton()
				.toggle(variation.canDelete);

			self.currentVariation(variation);
		},

		nextVariationName: function(name) {

			var match = false,
				name = $.trim(name.toLowerCase());

			$.each(self.item.meta.variations, function(i, variation) {

				if (name==variation.name.toLowerCase()) {

					match = true;

					var suffix = name.substr(-1, 1);

					name = ($.isNumeric(suffix)) ?
								name.substr(0, name.length - 1) + (parseInt(suffix, 10) + 1) :
								name + 1;

					return false;
				}
			});

			return (match) ? self.nextVariationName(name) : name;
		},

		//
		// Variation form
		//

		"{addVariationButton} click": function() {

			var variation = self.currentVariation(),
				variationName = $.String.capitalize(self.nextVariationName(variation.name));

			self.imageVariationList().hide();

			self.newVariationName()
				.data("default", variationName)
				.val(variationName)
				.select();

			self.newVariationWidth()
				.data("default", variation.width)
				.val(variation.width);

			self.newVariationHeight()
				.data("default", variation.height)
				.val(variation.height);

			self.imageVariationForm().show();
		},

		"{newVariationRatio} click": function(el) {

			el.toggleClass("locked");

			// Recalculate aspect ratio
			if (el.hasClass("locked")) {

				self.newVariationWidth()
					.keyup();
			}
		},

		sanitizeVariationName: function(deep) {

			var newVariationName = self.newVariationName();

			// Only allow a-zA-Z0-9. No space or symbols.
			var variationName = newVariationName.val().replace(self.options.variationNameFilter, "");

			if (deep) {

				// Prevent existing name conflicts
				variationName = self.nextVariationName(variationName);

				// Capitalize variation name
				newVariationName.val($.String.capitalize(variationName));
			}
		},

		"{newVariationName} keyup": function(el) {

			self.sanitizeVariationName();
		},

		"{newVariationName} blur": function(el) {

			self.sanitizeVariationName(true);
		},

		"[{newVariationWidth}, {newVariationHeight}] keyup": function(el, event) {

			var val = $.trim(el.val()),
				ratioLocked = self.newVariationRatio().is(".locked");

			// When a user presses backspace until the input is empty,
			// leave it empty instead of resetting it to default value.
			if (val=="") {

				if (ratioLocked) {
					self.newVariationWidth().val("");
					self.newVariationHeight().val("");
				}

				return;
			}

			var val = val.replace(new RegExp('[^0-9]','g'), ""),

				defaultVal = el.data("default"),

				val = val || defaultVal;

			el.val(val);

			// Do not calculate if aspect ratio is unlocked
			// or user is hitting the tab key.
			if (!ratioLocked || event.keyCode==9) {
				return;
			}

			var ratio;

			if (val==defaultVal) {

				ratio = 1

			} else {

				ratio = defaultVal / (parseInt(val, 10) || 0);
			}

			var width = self.newVariationWidth(),
				height = self.newVariationHeight();

			if (el[0]==width[0]) {
				height
					.val(Math.floor(height.data("default") / ratio));
			}

			if (el[0]==height[0]) {
				width
					.val(Math.floor(width.data("default") / ratio));
			}
		},

		"{backVariationButton} click": function() {

			self.imageVariationForm().hide();

			self.imageVariationList().show();
		},

		"{createVariationButton} click": function() {

			EasyBlog.ajax(
				"site.views.media.createVariation",
				{
					path: self.item.meta.path,
					place: self.place.id,
					name: self.newVariationName().val(),
					width: self.newVariationWidth().val(),
					height: self.newVariationHeight().val()
				},
				{
					success: function( variation ) {

						// Get the content for each variations.
						var content = self.view.variation({
							"variation" : variation
						}).data( 'variation' , variation );

						// Add variation to meta.
						self.item.meta.variations.push(variation);

						// Append the variation on the panel.
						self.imageVariations().prepend( content );

						// Bind as active item.
						$(content).click();

						// Hide the form.
						self.backVariationButton().click();
					},
					fail: function( message ) {

						self.imageVariationMessage().html( message );
					}
				}
			);
		},

		"{removeVariationButton} click": function() {

			var imageVariation = self.imageVariation(".active"),
				variation = imageVariation.data("variation");

			if (variation.canDelete) {

				EasyBlog.ajax(

					"site.views.media.deleteVariation",

					{
						"fromPath": self.item.meta.path,
						"place": self.place.id,
						'name': variation.name
					},

					{
						beforeSend: function() {

							imageVariation.addClass("busy");
						},

						success: function() {

							// Once the item is successfully removed, we need to remove this variation.
							imageVariation.slideUp(function(){

								imageVariation.remove();
							});

							// Revert to default image variation
							self.imageVariation(".default")
								.click();
						},

						fail: function(message) {

							try { console.log(message); } catch(e) {};
						},

						complete: function() {

							imageVariation.removeClass("busy");
						}
					}
				);
			}
		},


		//
		// Resize helpers
		//

		resizeWithin: function(sourceWidth, sourceHeight, maxWidth, maxHeight) {

			var targetWidth = sourceWidth,
				targetHeight = sourceHeight;

			if (targetWidth > maxWidth)
			{
				var ratio = maxWidth / sourceWidth;

				targetWidth  = sourceWidth  * ratio;
				targetHeight = sourceHeight * ratio;
			}

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

		//
		// Image preview/zooming
		//

		preloadImage: function(url, callback) {

			var image = $(new Image());

			image
				.load(function() {

					// Insert into background
					image
						.css({
							position: "absolute",
							left: "-9999px"
						})
						.appendTo("body");

					callback && callback.apply(self, [image.width(), image.height()]);

					image.remove();
				})
				.attr("src", url);

			return image;
		},

		previewImage: function() {

			var imagePreview = self.imagePreview();

			imagePreview.addClass("busy");

			self.preloadImage(

				self.item.meta.thumbnail.url,

				function(width, height) {

					self.imageEnforceWidth().data("default", width);
					self.imageEnforceHeight().data("default", height);
					self.imageEnforceWidth().trigger("keyup");

					var maxWidth  = imagePreview.width(),
						maxHeight = imagePreview.height(),

						size = self.resizeWithin(
							width,
							height,
							maxWidth,
							maxHeight
						);

					// Display image
					imagePreview
						.animate(
							{
								opacity: 0
							},
							function() {

								imagePreview
									.removeClass("busy")
									.css({
										width: size.width,
										height: size.height,
										top: (maxHeight - size.height) / 2,
										left: (maxWidth - size.width) / 2
									})
									.load(function(){
										imagePreview.animate(
											{
												opacity: 1
											});
									})
									.attr({
										src: self.item.meta.thumbnail.url
									});
							}
						);
				}
			);
		},

		zoomImage: function() {

			var variation = self.currentVariation();

			$.dialog({
				title: $.String.capitalize(variation.name),
				content: $.Deferred(),
				showOverlay: false,

				body: {
					css: {
						padding: 0
					}
				}
			});

			self.preloadImage(

				variation.url,

				function(width, height) {

					var image = $(new Image());

					image
						.attr({
							src: variation.url,
							width: width,
							height: height
						});

					$.dialog({
						content: image.toHTML(),
						width: width,
						height: height,
						body: {
							css: {
								minWidth: 0,
								minHeight: 0
							}
						}
					});
				}
			);
		},

		canHideZoomButton: false,

		zoomButtonVisible: false,

		"[{imagePreview}, {imageZoomButton}] mouseover": function() {

			if (self.imagePreview().is(".busy")) {
				return;
			}

			if (self.zoomButtonVisible && self.canHideZoomButton) {

				self.canHideZoomButton = false;
				return;
			}

			self.zoomButtonVisible = true;

			self.imageZoomButton()
				.css({
					opacity: 0
				})
				.show()
				.position({
					my: "right top",
					at: "right top",
					offset: "-10px 10px",
					of: self.imagePreview()
				})
				.css({
					left: "-=10px"
				})
				.animate({
					opacity: 1,
					left: "+=10px"
				});
		},

		"[{imagePreview}, {imageZoomButton}] mouseout": function() {

			self.canHideZoomButton = true;

			setTimeout(function(){

				if (self.canHideZoomButton) {

					self.imageZoomButton()
						.hide();

					self.zoomButtonVisible = false;
				}

			}, 250);
		},

		"[{imagePreview}, {imageZoomButton}] click": function() {

			self.zoomImage();
		},

		//
		// Insert options
		//

		// "[{imageZoomOption}, {imageCaptionOption}] change": function(el, event) {

		// 	event.stopPropagation();

		// 	el.parent(".field").toggleClass("hide-field-content", !el.is(":checked"));
		// },

		// "[{imageEnforceDimensionOption} change": function(el, event) {

		// 	event.stopPropagation();

		// 	el.parent(".field").toggleClass("hide-field-content", !el.is(":checked"));

		// 	self.imageEnforceWidth().trigger("keyup");
		// },

		"[{imageEnforceWidth}, {imageEnforceHeight}] keyup": function(el, event) {

			var val = $.trim(el.val());

			// When a user presses backspace until the input is empty,
			// leave it empty instead of resetting it to default value.
			if (val=="") {

				self.imageEnforceWidth().val("");
				self.imageEnforceHeight().val("");
				return;
			}

			var val = val.replace(new RegExp('[^0-9]','g'), ""),

				defaultVal = el.data("default") || el.attr("initial"),

				val = val || defaultVal;

			el.val(val);

			// Do not calculate if user is hitting the tab key.
			if (event.keyCode==9) {
				return;
			}

			var ratio;

			if (val==defaultVal) {

				ratio = 1

			} else {

				ratio = defaultVal / (parseInt(val, 10) || 0);
			}

			var width = self.imageEnforceWidth(),
				height = self.imageEnforceHeight();

			if (el[0]==width[0]) {

				height.val(Math.floor((height.data("default") || height.attr("initial")) / ratio));
			}

			if (el[0]==height[0]) {

				width.val(Math.floor((width.data("default") || width.attr("initial")) / ratio));
			}
		},

		"{imageCaptionOption} mouseup": function() {

			setTimeout(function(){

				self.imageCaption().focus()[0].select();

			}, 1);
		}
	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end
