dispatch.to("Foundry/2.1 Core Plugins").at(function($, manifest) {

/**
* jquery.uid
* Generates a unique id with optional prefix/suffix.
* https://github.com/jstonne/jquery.uid
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
*
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/

$.uid = function(p,s) {
	return ((p) ? p : '') + Math.random().toString().replace('.','') + ((s) ? s : '');
};
/**
* jquery.isDeferred
* Tests if an object is a jQuery Deferred object.
* https://github.com/jstonne/jquery.isDeferred
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
*
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/

$.isDeferred = function(obj) {
	return obj && $.isFunction(obj.always);
};
/**
 * jquery.distinct
 * Enhanced version of jQuery.unique that also removes
 * removes object/string/integer duplicates within an array.
 * https://github.com/jstonne/jquery.distinct
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

$.distinct = function(items) {

	var uniqueElements = $.unique;

	if (items.length < 1) {
		return;
	};

	// If item is an array of DOM elements
	if (items[0].nodeType) {

		return uniqueElements.apply(this, arguments);
	};

	// If item is an array of objects
	if (typeof items[0]=='object') {

		var unique = Math.random(),
			uniqueObjects = [];

		$.each(items, function(i) {

			if (!items[i][unique]) {

				uniqueObjects.push(items[i]);

				items[i][unique] = true;
			}
		});

		$.each(uniqueObjects, function(i) {

			delete uniqueObjects[i][unique];
		});

		return uniqueObjects;
	};

	// Anything else (can be combination of string, integers and boolean)
	return $.grep(items, function(item, i) {

		return $.inArray(item, items) === i;
	});

};
/**
* jquery.trimSeparators
* Trims whitespace and separators.
* https://github.com/jstonne/jquery.trimSeparators
*
* Turns this: ",df        ,,,  ,,,abc, sdasd sdfsdf    ,   asdsad, ,, , "
* into this : "df,abc,sdasd sdfsdf,asdsad"
*
* Requires jquery.distinct
* https://github.com/jstonne/jquery.distinct
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
*
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/

$.trimSeparators = function(keyword, separator, removeDuplicates) {

	var s = separator;

	keyword = keyword
		.replace(new RegExp('^['+s+'\\s]+|['+s+',\\s]+$','g'), '') // /^[,\s]+|[,\s]+$/g
		.replace(new RegExp(s+'['+s+'\\s]*'+s,'g'), s)             // /,[,\s]*,/g
		.replace(new RegExp('[\\s]+'+s,'g'), s)                    // /[\s]+,/g
		.replace(new RegExp(s+'[\\s]+','g'), s);                   // /,[\s]+/g

	if (removeDuplicates) {
		keyword = $.distinct(keyword.split(s)).join(s);
	}

	return keyword;
};
/*!
 * jquery.number.
 * Helpers for numbers.
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

$.isNumeric = function(n) {
	// http://stackoverflow.com/questions/18082/validate-numbers-in-javascript-isnumeric
	return !isNaN(parseFloat(n)) && isFinite(n);
}

$.Number = {
	rotate: function(n, min, max, offset) {
		if (offset===undefined)
			offset = 0;

		n += offset;
		if (n < min) {
			n += max + 1;
		} else if (n > max) {
			n -= max + 1;
		}

		return n;
	}
}
/**
* jquery.stretchToFit
* Stretch any element to fit its parent container.
* https://github.com/jstonne/jquery.stretchToFit
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
*
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/

$.fn.stretchToFit = function() {
	return $.each(this, function()
	{
		var $this = $(this);

		$this
			.css('width', '100%')
			.css('width', $this.width() * 2 - $this.outerWidth(true) - parseInt($this.css('borderLeftWidth')) - parseInt($this.css('borderRightWidth')));
	});
}

/**
* jquery.uid
* Get the HTML of the current element.
*
* https://github.com/jstonne/jquery.toHTML
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
*
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/

$.fn.toHTML = function() {
	return $('<div>').html(this).html();
}

}); // dispatch: end