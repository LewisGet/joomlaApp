// module: start
EasyBlog.module("media/place", function($) {

var module = this;



// controller: start
EasyBlog.Controller(

	"Media.Place",

	{
		defaultOptions: {

			initialPlace: "",

			places: [],

			"{placeMenuGroup}": ".placeMenuGroup",
			"{placeMenu}"     : ".placeMenu",
			"{placeBodyGroup}": ".placeBodyGroup",
			"{placeBody}"     : ".placeBody",

			view: {
				placeMenu: "media/place.menu",
				placeBody: "media/place.body"
			}
		}
	},

	function(self) { return {

		init: function() {

			// Load all places
			$.each(self.options.places, function(i, place) {

				self.addPlace(place);
			});

			self.setLayout();
		},

		setLayout: function() {

			if (self.currentPlace) {

				self.currentPlace.setLayout && self.currentPlace.setLayout();
			}
		},

		places: {},

		loadPlace: function(placeName, callback) {

			var loadTask =
				EasyBlog.require()
					.script("media/place." + placeName.toLowerCase())
					.done(callback);

			return loadTask;
		},

		addPlace: function(place) {

			var placeName 	= place.name,

				placeTitle	= place.title,

				placeMenu = self.view.placeMenu({title: placeTitle})
								.addClass(place.name)
								.appendTo(self.placeMenuGroup()),

				placeBody = self.view.placeBody()
								.addClass(place.name)
								.appendTo(self.placeBodyGroup()),

				placeOptions = place.options,

				placeId = (placeOptions) ? placeOptions.id : placeName,

				initPlace = function() {

					var PlaceController = EasyBlog.Controller.Media.Place[placeName];

					// Place is implemented as a secondary controller
					// to the the media manager element.
					var place = new PlaceController(

						self.element,

						$.extend(
							{
								controller: {
									id: placeId,
									media: self.media,
									name: placeName,
									title: placeTitle,
									menu: placeMenu,
									body: placeBody
								}
							},
							placeOptions
						)
					);

					place.menu.data("place", place);

					place.body.data("place", place);

					self.places[placeId] = place;

					if (place.name===self.options.initialPlace) {

						self.togglePlace(place, true);
					}
				};


			// Get the place controller
			var PlaceController = EasyBlog.Controller.Media.Place[placeName];

			// If the place controller is not loaded
			if (PlaceController==undefined) {

				// Load it first before initializing the place
				self.loadPlace(placeName, initPlace);

			} else {

				// Else intialize the place now.
				initPlace();
			}

			return place;
		},

		getPlace: function(placeId) {

			return self.places[placeId];
		},

		activate: function(place) {

			self.togglePlace(place, true);
		},

		deactivate: function(place) {

			// If the place is deactivated, don't hae to do anything.
			if (self.currentPlace!==place) return;

			self.togglePlace(place);
		},

		togglePlace: function(place, forceActivate) {

			if (place===undefined) return;

			var fromPlace = self.currentPlace,
				toPlace = place,
				canLeavePlace = true;

			if (fromPlace == toPlace && !forceActivate) {
				toPlace = undefined;
			}

			if (fromPlace && fromPlace != toPlace) {

				// See if onPlaceLeave event is available
				if ($.isFunction(fromPlace.onPlaceLeave)) {

					// Trigger the onPlaceLeave event and see if currentPlace allows it.
					canLeavePlace = !(fromPlace.onPlaceLeave(toPlace) === false);
				}

				if (!canLeavePlace) return;

				fromPlace.menu.add(fromPlace.body)
					.removeClass("active");
			}

			self.currentPlace = toPlace;

			if (toPlace) {

				toPlace.menu.add(toPlace.body)
					.addClass("active");

				// When window resize happens, it only resizes the current active place.
				// Calling setLayout after switching to another place that was previously opened,
				// to ensure the new place reflects the new window dimension.
				self.setLayout();

				if ($.isFunction(toPlace.onPlaceEnter)) {

					toPlace.onPlaceEnter(fromPlace);
				}
			}
		},

		"{placeMenu} click": function(el) {

			var place = el.data("place");

			if (place!==undefined) {

				// Disable deactivation of places.
				if (place===self.currentPlace) return;

				self.togglePlace(place);
			}
		}
	}}

);
// controller: end

module.resolve();

});
// module: end
