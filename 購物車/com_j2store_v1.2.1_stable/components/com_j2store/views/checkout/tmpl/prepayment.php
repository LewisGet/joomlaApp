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
defined('_JEXEC') or die('Restricted access');

$edit_link = JRoute::_('index.php?option=com_j2store&view=checkout&task=begin');

$order = @$this->order;
if(count($this->bill_address))
	 $billAddr = (object) $this->bill_address;
if(count($this->ship_address))
	$shipAddr = (object) $this->ship_address;

$plugin_html = @$this->plugin_html;
?>
<div class="j2store_order_review">

<div class='componentheading'>
	<span><?php echo JText::_( "J2STORE_REVIEW_CHECKOUT" ); ?>
	</span>
</div>

<!--    ORDER SUMMARY   -->
<h3>
	<?php echo JText::_("J2STORE_ORDER_SUMMARY") ?>
</h3>
<div class="j2storeOrderSummary">
	<?php echo @$this->orderSummary; ?>
</div>

<?php if(
		(($this->params->get('show_billing_address') || $this->params->get('allow_guest_checkout') ) && $billAddr) ||
		($this->params->get('show_shipping_address') && $shipAddr)
	)
{ ?>

<table class="billing_shipping" width="100%">
	<tr>
		<?php
		if(($this->params->get('show_billing_address') || $this->params->get('allow_guest_checkout') ) && $billAddr){
			echo '<td width="50%">';
			echo '<h3>'. JText::_('J2STORE_BILLING_ADDRESS') .'</h3>';
			echo $billAddr->first_name." ".$billAddr->last_name."<br/>";
			echo $billAddr->address_1.", ";
			echo $billAddr->address_2 ? $billAddr->address_2.", " : "<br/>";
			echo $billAddr->city.", ";
			echo $billAddr->zone_name ? $billAddr->zone_name." - " : "";
			echo $billAddr->zip." <br/>";
			echo $billAddr->country_name." <br/> ";
			echo (!empty($billAddr->phone_1)&&!empty($billAddr->phone_2))?JText::_('J2STORE_TELEPHONE').":":' ';
			echo $billAddr->phone_1." , ";
			echo $billAddr->phone_2 ? $billAddr->phone_2.", " : "<br/> ";
			//echo $billAddr->email ? JText::_('email').": ".$billAddr->email."<br/>" : "";
			echo '</td>';	
		}
		?>
		
		
		<?php
		if($this->params->get('show_shipping_address') && $shipAddr){
			echo '<td>';
			echo '<h3>'. JText::_('J2STORE_SHIPPING_ADDRESS') .'</h3>';
			echo $shipAddr->first_name." ".$shipAddr->last_name."<br/>";
			echo $shipAddr->address_1.", ";
			echo $shipAddr->address_2 ? $shipAddr->address_2.", " : "<br/>";
			echo $shipAddr->city.", ";
			echo $shipAddr->zone_name ? $shipAddr->zone_name." - " : "";
			echo $shipAddr->zip." <br/>";
			echo $shipAddr->country_name." <br/> ";
			echo (!empty($billAddr->phone_1)&&!empty($billAddr->phone_2))?JText::_('J2STORE_TELEPHONE').":":' ';
			echo $shipAddr->phone_1." , ";
			echo $shipAddr->phone_2 ? $shipAddr->phone_2.", " : "";	
			echo '</td>';
		}
		?>
		
				
</tr>
<tr><td colspan="2"><div class="edit_address_link" style="text-align:right;padding:3px;">
				<a href="<?php echo $edit_link; ?>" >
				  <?php echo JText::_('J2STORE_EDIT_ADDRESS'); ?>
				  </a>
				  </div></td></tr>
				  </table>

<div class="reset"></div>
<?php }?>
<!--    PAYMENT METHOD   -->
<h3>
	<?php echo JText::_("J2STORE_PAYMENT_METHOD"); ?>
</h3>

<?php echo $plugin_html; ?>

</div>