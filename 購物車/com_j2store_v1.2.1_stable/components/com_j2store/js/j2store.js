window.addEvent('domready', function() {

	var container = 'miniJ2StoreCart';
	var cartcontainer = '.cart_form';

	if (document.id(container)) {
		doMiniCart();
	}
	
	
	//check for billing address
	var sameAsBilling = 'shipping_make_same';
	if(document.id(sameAsBilling)) {
	if(document.id(sameAsBilling).checked) {
		document.id('j2store_shipping_section').set({
		    // the 'styles' property passes the object to Element:setStyles.
		    styles: {
		        visible: 'visible',
		        display: 'none'
		    }
		});
		document.id('j2store_shipping_section').getElements(".input-label").removeClass("required");
		document.id('j2store_shipping_section').getElements(".input-text").removeClass("required");
		}
	}
}); // end dom ready

function doMiniCart() {
	var container = 'miniJ2StoreCart';
	var murl = j2storeURL + 'index.php?option=com_j2store&view=mycart&task=ajaxmini';
	var a = new Request.HTML( {
		url : murl,
		method : "post",
		onSuccess : function(responseTree, responseElements, responseHTML,
				responseJavaScript) {
			if (document.id(container)) {
				document.id(container).set('html', responseHTML);
			}
		}

	}).send();
}

function j2storeGetPaymentForm(element, container) {
	var url = j2storeURL
			+ 'index.php?option=com_j2store&view=checkout&task=getPaymentForm&format=raw&payment_element='
			+ element;
	j2storeDoTask(url, container, document.adminForm);
}

function j2storeDoTask(url, container, form, msg) {
	j2storeNewModal(msg);

	// if url is present, do validation
	if (url && form) {
		// loop through form elements and prepare an array of objects for
		// passing to server
		var str = new Array();
		for (i = 0; i < form.elements.length; i++) {
			postvar = {
				name : form.elements[i].name,
				value : form.elements[i].value,
				checked : form.elements[i].checked,
				id : form.elements[i].id
			};
			str[i] = postvar;
		}
		// execute Ajax request to server
		var a = new Request.JSON( {
			url : url,
			method : "post",
			// data:{"elements":Json.toString(str)},
			onComplete : function(response) {
				if (document.id(container)) {
					document.id(container).set('html', response.msg);
				}
				(function() {
					document.body.removeChild(document.id('j2storeModal'));
				}).delay(500);
				return true;
			}
		}).send();
	} else if (url && !form) {
		// execute Ajax request to server
		var a = new Request.JSON( {
			url : url,
			method : "post",
			onComplete : function(response) {
				if (document.id(container)) {
					document.id(container).set('html', response.msg);
				}
				(function() {
					document.body.removeChild(document.id('j2storeModal'));
				}).delay(500);
			}
		}).send();
	}
}

/**
 * 
 * @param {String}
 *            msg message for the modal div (optional)
 */
function j2storeNewModal(msg) {
	if (typeof window.innerWidth != 'undefined') {
		var h = window.innerHeight;
		var w = window.innerWidth;
	} else {
		var h = document.documentElement.clientHeight;
		var w = document.documentElement.clientWidth;
	}
	var t = (h / 2) - 15;
	var l = (w / 2) - 15;
	var i = document.createElement('img');
	var src = j2storeURL+ 'components/com_j2store/images/ajax-loader.gif';
	i.src = src;
	i.style.position = 'absolute';
	i.style.top = t + 'px';
	i.style.left = l + 'px';
	i.style.backgroundColor = '#000000';
	i.style.zIndex = '100001';
	var d = document.createElement('div');
	d.id = 'j2storeModal';
	d.style.position = 'fixed';
	d.style.top = '0px';
	d.style.left = '0px';
	d.style.width = w + 'px';
	d.style.height = h + 'px';
	d.style.backgroundColor = '#000000';
	d.style.opacity = 0.5;
	d.style.filter = 'alpha(opacity=50)';
	d.style.zIndex = '100000';
	d.appendChild(i);
	if (msg != '' && msg != null) {
		var m = document.createElement('div');
		m.style.position = 'absolute';
		m.style.width = '200px';
		m.style.top = t + 50 + 'px';
		m.style.left = (w / 2) - 100 + 'px';
		m.style.textAlign = 'center';
		m.style.zIndex = '100002';
		m.style.fontSize = '1.2em';
		m.style.color = '#ffffff';
		m.innerHTML = msg;
		d.appendChild(m);
	}
	document.body.appendChild(d);
}

function j2storeCartRemove(event, cart_id, product_id, pop_up) {
	this.stop();
	var row = event.getParent().getParent();
	var container;
	if (pop_up == 1) {
		container = document.id('sbox-content');
	} else {
		container = document.id('j2storeCartPopup');
	}

	// minicart
	var minicontainer = 'miniJ2StoreCart';

	var myurl = 'index.php?option=com_j2store&view=mycart&task=update&popup=' + pop_up;
	var myAjax = new Request.HTML(
			{
				url : myurl,
				method : "get",
				data : "remove=1&cid[" + cart_id + "]=" + product_id,
				update : container,
				onSuccess : function(response) {
					if (document.id(minicontainer)) {
						doMiniCart();
					}
				},// onSuccess
				onFailure : function() {
					window
							.location("index.php?option=com_j2store&view=mycart&task=update&remove=1&cid["
									+ cart_id + "]=" + product_id);
				}// onFailure
			}).send();
}

function j2storeCartChange(event, orderCode) {
	var row = event.getParent().getParent();
	var q = event.value;
	if (q == '')
		q = 0;
	var quantity = isNaN(q) ? 0 : parseInt(q);
	var myurl = 'index.php?option=com_j2store&view=mycart&task=ajax';
	if (quantity != 0) {
		// quantity is not zero..so setup the ajax call

		var myAjax = new Request.HTML(
				{
					method : 'get',
					url : myurl,
					data : 'quantity[' + orderCode + ']=' + quantity,
					update : document.id('j2store_container'),
					// when the request is made, turn the row text colour RED
					onRequest : function() {
						new Fx.Style(row, 'color', {
							duration : 300
						}).start('#000000', '#fb6c6c');
					},
					// when ajax call successful, turn the text colour of the
					// row back to black
					// and update the prices/totals/etc.
					onSuccess : function(response) {
						// start the effect
					},// onSuccess
					// if ajax call fails, then force a page refresh straight to
					// com_j2store
					onFailure : function() {
						window.location("index.php?option=com_j2store&view=mycart&quantity["
										+ orderCode + "]=" + quantity);
					}// onFailure
				}).send();// var myAjax
	}// if quantity
}

function j2storeAddToCart(url, task, form, doModal, msg) {
	var container = 'miniJ2StoreCart';
	// if (doModal == true) { j2storeNewModal(msg); }

	if (task == 'addtocart') {
		// loop through form elements and prepare an array of objects for
		// passing to server
		var str = new Array();
		//form=form[0];
		for (i = 0; i < form.elements.length; i++) {
			postvar = {
				name : form.elements[i].name,
				value : form.elements[i].value,
				checked : form.elements[i].checked,
				id : form.elements[i].id
			};
			str[i] = postvar;
		}

		// execute Ajax request to server
		var url1 = 'index.php?option=com_j2store&view=mycart&task=ajax&tmpl=component';
		var ajreq1 = new Request.HTML( {
			url : url1,
			method : 'post',
			data : {
				'elements' : JSON.encode(str)
			},
			onSuccess : function(responseTree, responseElements, responseHTML,
					responseJavaScript) {
				SqueezeBox.setContent('iframe', responseHTML);
				SqueezeBox.applyContent(responseHTML);
				if (document.id(container)) {
					doMiniCart();
				}
				//timeoutID = setTimeout( 'SqueezeBox.close()', 3000 );
			}
		}).send();
	} else {
		if (doModal == true) {
			(function() {
				document.body.removeChild(document.id('j2storeModal'));
			}).delay(500);
		}
		form.task.value = task;
		form.submit();
	}
}



function getAjaxZone(field_name,field_id,country_value, default_zid) {	
			//var zone_value = document.id('jformzone_id').value;
			//if(zone_value)
			 // zvalue = zone_value;
			//else 
			 //   zvalue = 0;
			var url = j2storeURL+'index.php?option=com_j2store&view=checkout&task=ajaxGetZoneList';
			var obj =new Request.HTML({
				url:url,	
				update: field_name.substring(0,4)+'ZoneList',
				method: 'post',
				data:{
					'country_id':country_value,
					'zone_id':default_zid,
					'field_name':field_name,
					'field_id':field_id
				},
				//onRequest: function() { document.id('listproduct').set('text', 'loading...'); },
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) { 
					//document.id('zoneList').
					}
				}).send();
		}

