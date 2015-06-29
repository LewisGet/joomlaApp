<?php
/*------------------------------------------------------------------------
 # com_j2store - J2 Store v 1.0
# ------------------------------------------------------------------------
# author    Sasi varna kumar - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2012 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://j2store.org
# Technical Support:  Forum - http://j2store.org/forum/index.html
-------------------------------------------------------------------------*/


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$action = JRoute::_('index.php?option=com_j2store&view=checkout');

$j2params = $this->params;

$doc = &JFactory::getDocument();
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
if($this->params->get('show_billing_address') && $this->params->get('show_shipping_address') ) {

	$script = "

	window.addEvent('domready', function() {

	//check for billing address
	var sameAsBilling = 'shipping_make_same';
	if(document.id(sameAsBilling)) {
	if(document.id(sameAsBilling).checked) {
	document.id('j2store_shipping_section').set({
	styles: {
	visible: 'visible',
	display: 'none'
}
});
}
}
});";
	$doc->addScriptDeclaration($script);
}

//shipping address form check
if($this->ship_address) {
	$checked ='';
} else {
 	$checked ='checked="checked"';
}
?>

<script type="text/javascript">
<!--

window.addEvent('domready', function() {
	var billing_zone_id;
	var shipping_zone_id;

	<?php if($this->bill_address->zone_id) { ?>
		billing_zone_id = <?php echo $this->bill_address->zone_id; ?>;
	<?php } else { ?>
		billing_zone_id=0;
	<?php } ?>

	if(document.id('billing:country')) {
		getAjaxZone('billing[zone_id]','billing:zone', document.id('billing:country').value, billing_zone_id);

		document.id('billing:country').addEvents({
		change:function() {
			getAjaxZone('billing[zone_id]','billing:zone',this.value, billing_zone_id);
		},
		load:function() {
			getAjaxZone('billing[zone_id]','billing:zone', document.id('billing:country').value, billing_zone_id);
		}
	});
	}

	<?php if($this->ship_address->zone_id) { ?>
	shipping_zone_id = <?php echo $this->ship_address->zone_id; ?>;
	<?php } else { ?>
	shipping_zone_id=0;
	<?php } ?>

	 	if(document.id('shipping:country')) {
	 		getAjaxZone('shipping[zone_id]','shipping:zone', document.id('shipping:country').value, shipping_zone_id);
		document.id('shipping:country').addEvents({
			change:function() {
				getAjaxZone('shipping[zone_id]','shipping:zone',this.value, shipping_zone_id);
			},
			load:function() {
				getAjaxZone('shipping[zone_id]','shipping:zone', document.id('shipping:country').value, shipping_zone_id);
			}
		});

		}

	});

function myValidate(f) {

   if (document.formvalidator.isValid(f)) {
	   var error = 0;

	   var sameAsBilling = 'shipping_make_same';
		if(document.id(sameAsBilling)) {
		if(document.id(sameAsBilling).checked) {
		//	return true;
		} else {

			if(document.id('shipping:firstname').get('value') == '') {
				document.id('shipping:firstname').addClass('valid-fail');
				error = 1;
			}

			<?php if($j2params->get('ship_lname') == 1):?>
			if(document.id('shipping:lastname').get('value') == '') {
				document.id('shipping:lastname').addClass('valid-fail');
				error = 1;
			}
			<?php endif; ?>

			<?php if($j2params->get('ship_addr_line1') == 1):?>
			if(document.id('shipping:address_1').get('value') == '') {
				document.id('shipping:address_1').addClass('valid-fail');
				error = 1;
			}
			<?php endif; ?>

			<?php if($j2params->get('ship_zip') == 1):?>
			if(document.id('shipping:zip').get('value') == '') {
				document.id('shipping:zip').addClass('valid-fail');
				error = 1;
			}
			<?php endif; ?>

			<?php if($j2params->get('ship_city') == 1):?>
			if(document.id('shipping:city').get('value') == '') {
				document.id('shipping:city').addClass('valid-fail');
				error = 1;
			}
			<?php endif; ?>

			<?php if($j2params->get('ship_country_zone') == 1):?>
			if(document.id('shipping:country').get('value') == '') {
				document.id('shipping:country').addClass('valid-fail');
				error = 1;
			}
			<?php endif; ?>

			<?php if($j2params->get('ship_phone1') == 1):?>
			if(document.id('shipping:phone_1').get('value') == '') {
				document.id('shipping:phone_1').addClass('valid-fail');
				error = 1;
			}
			<?php endif; ?>

			<?php if($j2params->get('ship_phone2') == 1):?>
			if(document.id('shipping:phone_2').get('value') == '') {
				document.id('shipping:phone_2').addClass('valid-fail');
				error = 1;
			}
			<?php endif; ?>

			if(error ==1) {
				return false;
			}
		}
		}

		<?php if (count($this->plugins) > 1 && $this->showPayment) { ?>
		var result = getCheckedButton('payment_plugin', f);

		   if (result) {
			  // return true;
		   } else {
		   	alert('<?php echo JText::_('J2STORE_SELECT_A_PAYMENT_METHOD'); ?>');
		   	return false;
		  }
		<?php } ?>

	  return true;
   }
   else {
      var msg = <?php echo JText::_('J2STORE_FILL_ALL_REQUIRED_FIELDS'); ?>;
      alert(msg);
   }
   return false;
}

function getCheckedButton(group, form) {
	if (typeof group == 'string') group = form.elements[group];

	for (var i = 0, n = group.length; i < n; ++i)
	if (group[i].checked) return group[i];
	return null;
}

function toggleShipping(el){
	    if(el.checked) {
	    	document.id('j2store_shipping_section').set({
				    // the 'styles' property passes the object to Element:setStyles.
				    styles: {
				        visible: 'hidden',
				        display: 'none'
				    }
			  });
			  document.id('shipping_make_same').set('value', 1);
			  document.id('shipping_make_same').set('checked', 'checked');

		  } else {
			 document.id('j2store_shipping_section').set({
				    // the 'styles' property passes the object to Element:setStyles.
				    styles: {
				        visible: 'visible',
				        display: 'block'
				    }

			  });

			var shipping_zone_id;
			 <?php if($this->ship_address->zone_id) { ?>
				shipping_zone_id = <?php echo $this->ship_address->zone_id; ?>;
			<?php } else { ?>
				shipping_zone_id=0;
			<?php } ?>

			  getAjaxZone('shipping[zone_id]','shipping:zone', document.id('shipping:country').value, shipping_zone_id);
			  document.id('shipping_make_same').set('value', 2);
			  document.id('shipping_make_same').removeProperty('checked');
		  }
	  }

-->
</script>



<div class="j2storeCheckout">

	<form action="<?php echo $action; ?>" method="post" id="adminform"
		class="form-validate" name="adminForm" enctype="multipart/form-data"
		onSubmit="return myValidate(this);">

		<?php if($this->params->get('show_billing_address') || $this->params->get('allow_guest_checkout') || $this->params->get('show_shipping_address')): ?>
		<div class="billing_shipping_fields">

			<?php if($this->params->get('show_billing_address') || $this->params->get('allow_guest_checkout')): ?>

			<div class="j2storeBillingAddress">
				<h3>
					<?php echo JText::_('J2STORE_BILLING_ADDRESS'); ?>
				</h3>
				<?php echo $this->loadTemplate('billing'); ?>

			</div>
			<?php endif; ?>

			<?php if($this->params->get('show_shipping_address')): ?>

			<div class="j2storeShippingAddress">

				<?php if(($this->params->get('show_billing_address') || $this->params->get('allow_guest_checkout'))  && $this->params->get('show_shipping_address')):?>
				<input id="shipping_make_same" value='1' name="shipping_make_same" type="checkbox"
					<?php echo $checked; ?> onClick="toggleShipping(this)" />
				<?php echo JText::_('J2STORE_MAKE_SHIPPING_SAME'); ?>
				<?php endif; ?>

				<?php echo $this->loadTemplate('shipping'); ?>
			</div>

			<?php endif; ?>
		</div>
		<?php endif; ?>

		<div class="shipping_payment_info_div_master">

			<div class="order_summary">
				<?php echo @$this->orderSummary; ?>
			</div>

			<?php if($this->showShipping): ?>
			<div class="shipping_info">
				<?php echo $this->loadTemplate('shippingmethod'); ?>
			</div>
			<?php endif; ?>

			<?php if($this->showPayment): ?>
			<div class="payment_info">
				<?php echo $this->loadTemplate('payment'); ?>
			</div>
			<?php endif; ?>

			<div class="order_comment">
				<?php echo $this->loadTemplate('comment'); ?>
				<input class="button" type="submit"
					value="<?php echo JText::_('J2STORE_PROCEED_TO_PAYMENT');?>" />
			</div>

		</div>


		<div style="float: right;">
				<input type="hidden" name="option" value="com_j2store" />
				<input type="hidden" name="controller" value="checkout" />
				<input type="hidden" name="task" value="preparePayment" />
				<input type="hidden" name="savetype" value="address" />
				<input type="hidden" name="guest" value="<?php echo @$this->guest;?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</div>
	</form>
</div>
