window.addEvent('domready', function() {
	var djItemPriceInput = document.id('jform_price');
	djItemPriceInput.addEvents({
		'keyup' : function(e){djValidatePrice(djItemPriceInput);},
		'change' : function(e){djValidatePrice(djItemPriceInput);},
		'click' : function(e){djValidatePrice(djItemPriceInput);}
	});
	
	var djFieldGroup = document.id('jform_group_id');
	djFieldGroup.onchange=(function(){
		djRenderForm();
	});
	/*djFieldGroup.addEvent('change',function(){
		djRenderForm();
	});*/
	
	//if (document.id('jform_id').value > 0 && document.id('jform_group_id').value > 0) {
		djRenderForm();
	//}
});

function djValidatePrice(priceInput) {
		var r = new RegExp("\,", "i");
		var t = new RegExp("[^0-9\,\.]+", "i");
		priceInput.setProperty('value', priceInput.getProperty('value')
				.replace(r, "."));
		priceInput.setProperty('value', priceInput.getProperty('value')
				.replace(t, ""));
}
function djRenderForm() {
	var itemId = document.id('jform_id').value;
	
	if (!itemId || itemId == 0) {
		var vars = {};
	    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
	        vars[key] = value;
	    });
	    if (vars['id'] > 0) {
	    	itemId = vars['id'];
	    }
	}
	var groupId= document.id('jform_group_id').value;
	
	if (typeof ( document.id('itemAttributes') ) !== 'undefined') {
		ajax = new Request(
				{
					url : 'index.php?option=com_djcatalog2&view=item&layout=extrafields&format=raw&itemId='
							+ itemId
							+ '&groupId='
							+ groupId,
					onSuccess : function(resp) {
						document.id('itemAttributes').innerHTML = resp;
					}.bind(this)
				});
		ajax.send.delay(10, this.ajax);
	}
}