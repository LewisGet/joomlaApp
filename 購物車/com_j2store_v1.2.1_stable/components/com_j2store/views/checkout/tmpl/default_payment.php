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
?>
<div id='onCheckoutPayment_wrapper'>
	<h3>
		<?php echo JText::_('J2STORE_SELECT_A_PAYMENT_METHOD'); ?>
	</h3>
	<?php
	if ($this->plugins)
	{
		foreach ($this->plugins as $plugin)
		{
			?>
	<input value="<?php echo $plugin->element; ?>"
		class="payment_plugin" name="payment_plugin" type="radio"
		onclick="j2storeGetPaymentForm('<?php echo $plugin->element; ?>', 'payment_form_div');"
		<?php echo (!empty($plugin->checked)) ? "checked" : ""; ?> />
	<?php echo JText::_($plugin->name ); ?>
	<br />
	<?php
		}
	}
	?>

</div>

<div id='payment_form_div' style="padding-top: 10px;">
	<?php
	if (!empty($this->payment_form_div))
	{
		echo $this->payment_form_div;
	}
	?>

</div>

<div
	id="validationmessage" style="padding-top: 10px;"></div>
