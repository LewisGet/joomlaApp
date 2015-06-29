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
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'popup.php');
?>

<h3>
	<?php echo JText::_('J2STORE_CUSTOMER_NOTE'); ?>
</h3>
<textarea name="customer_note" rows="3" cols="40"><?php echo $this->order->customer_note; ?></textarea>
<div id="checkbox_tos">
	<input type="checkbox" class="required" id="j2store_tos"
		name="j2store_tos" value="1" /> <label for="j2store_tos"> <?php 
		if(! $this->tos_link==null){
			echo J2StorePopup::popup($this->tos_link,JText::_('J2STORE_TERMS_AND_CONDITIONS'));
		} else{ echo JText::_('J2STORE_TERMS_AND_CONDITIONS');
} ?>
	</label>	
</div>
