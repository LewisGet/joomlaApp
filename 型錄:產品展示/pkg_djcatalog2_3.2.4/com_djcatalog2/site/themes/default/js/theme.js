/**
 * @version 2.1
 * @package DJ Catalog 2
 * @copyright Copyright (C) 2010 Blue Constant Media LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://design-joomla.eu
 * @author email contact@design-joomla.eu
 * @developer Michal Olczyk - michal.olczyk@design-joomla.eu
 * 
 * DJ Catalog 2 is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 * 
 * DJ Catalog 2 is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * DJ Catalog 2. If not, see <http://www.gnu.org/licenses/>.
 * 
 */

(function($) {
	function DJCatMatchModules(className, setLineHeight) {
		var maxHeight = 0;
		var divs = null;
		if (typeof(className) == 'string') {
			divs = $$(className);
		} else {
			divs = className;
		}
		if (divs.length > 0) {
			divs.each(function(element) {
				maxHeight = Math.max(maxHeight, parseInt(element.getStyle('height')));
			});
			
			divs.setStyle('height', maxHeight);
			if (setLineHeight) {
				divs.setStyle('line-height', maxHeight);
			}
		}
	}
	
	this.DJCatImageSwitcher = function (){
		var mainimagelink = $('djc_mainimagelink');
		var mainimage = $('djc_mainimage');
		var thumbs = $('djc_thumbnails') ? $('djc_thumbnails').getElements('img') : null;
		var thumblinks = $('djc_thumbnails') ? $('djc_thumbnails').getElements('a') : null;
		
		if(mainimagelink && mainimage) {
			mainimagelink.removeEvents('click').addEvent('click', function(evt) {
				var rel = mainimagelink.rel;
				$(rel).fireEvent('click', $(rel));

				if(!/android|iphone|ipod|series60|symbian|windows ce|blackberry/i.test(navigator.userAgent)) {
					return false;
				}
				return true;
			});
		}
		
		if (!mainimage || !mainimagelink || !thumblinks || !thumbs) return false;
		
		thumblinks.each(function(thumblink,index){
			var fx = new Fx.Tween(mainimage, {link: 'cancel', duration: 200});

			thumblink.addEvent('click',function(event){
				event.preventDefault();
				//new Event(element).stop();
				/*
				mainimage.onload = function() {
					fx.start('opacity',0,1);
				};
				*/
				var img = new Image();
				img.onload = function() {
					fx.start('opacity',0,1);
				};
				
				fx.start('opacity',1,0).chain(function(){
					mainimagelink.href = thumblink.href;
					mainimagelink.title = thumblink.title;
					mainimagelink.rel = 'djc_lb_'+index;
					img.src = thumblink.rel;
					mainimage.src = img.src;
					mainimage.alt = thumblink.title;
				});
				return false;
			});
		});
	}; 
	
	window.addEvent('domready', function(){
		DJCatImageSwitcher();
		
		// contact form handler
		var contactform = document.id('contactform');
		var makesure = document.id('djc_contact_form');
		var contactformButton = document.id('djc_contact_form_button');
		var contactformButtonClose = document.id('djc_contact_form_button_close');
		if (contactform && makesure) {
			var djc_formslider = new Fx.Slide('contactform',{
				duration: 200,
				resetHeight: true
			});
			
			if (window.location.hash == 'contactform' || window.location.hash == '#contactform') {
				djc_formslider.slideIn().chain(function(){
					if (djc_formslider.open == true) {
						var scrollTo = new Fx.Scroll(window).toElement('contactform');
					}
				});
			} else if (contactformButton) {
				djc_formslider.hide();
			}
			if (contactformButton) {
				contactformButton.addEvent('click', function(event) {
					event.stop();
					djc_formslider.slideIn().chain(function(){
						if (djc_formslider.open == true) {
							var scrollTo = new Fx.Scroll(window).toElement('contactform');
						}
					});
				});
			}
			if (contactformButtonClose) {
				contactformButtonClose.addEvent('click', function(event){
					event.stop();
					djc_formslider.slideOut().chain(function(){
						if (djc_formslider.open == false) {
							var scrollTo = new Fx.Scroll(window).toElement('djcatalog');
						}
					});
				});
			}
		}
	});

	window.addEvent('load', function() {
		DJCatMatchModules('.djc_subcategory_bg', false);
		//DJCatMatchModules('.djc_item_bg', false);
		DJCatMatchModules('.djc_thumbnail', true);
		
		if ($$('.djc_item_row')) {
			$$('.djc_item_row').each(function(row, index){
				var elements = row.getElements('.djc_item_bg');
				DJCatMatchModules(elements, false);
			});
		}
		
		var djcatpagebreak_acc = new Fx.Accordion('.djc_tabs .accordion-toggle',
				'.djc_tabs .accordion-body', {
					alwaysHide : false,
					display : 0,
					duration : 100,
					onActive : function(toggler, element) {
						toggler.addClass('active');
						element.addClass('in');
					},
					onBackground : function(toggler, element) {
						toggler.removeClass('active');
						element.removeClass('in');
					}
				});
		var djcatpagebreak_tab = new Fx.Accordion('.djc_tabs li.nav-toggler',
				'.djc_tabs div.tab-pane', {
					alwaysHide : true,
					display : 0,
					duration : 150,
					onActive : function(toggler, element) {
						toggler.addClass('active');
						element.addClass('active');
					},
					onBackground : function(toggler, element) {
						toggler.removeClass('active');
						element.removeClass('active');
					}
				});
	});
})(document.id);
