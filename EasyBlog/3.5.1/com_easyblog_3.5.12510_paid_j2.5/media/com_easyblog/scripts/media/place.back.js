EasyBlog.module("media/place.back",function(e){var t=this;EasyBlog.Controller("Media.Place.Back",{defaultOptions:{}},function(e){return{init:function(){e.media.dashboard||e.menu.hide()},onPlaceEnter:function(t){var n=e.media.dashboard;e.media.places.activate(t),n&&n.media.hideManager()}}}),t.resolve()});