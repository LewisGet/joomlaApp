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

	<h3>
		<?php echo JText::_('J2STORE_SHIPPING_INFORMATION'); ?>
	</h3>
	<?php $sm = $this->order->shipping->shipping_name; 
	if($sm=='Flat Rate Per Order'){
		echo JText::_('J2STORE_SHIPM_FLAT_RATE_PER_ORDER');
	}else if($sm=='Quantity Based Per Order'){
		echo JText::_('J2STORE_SHIPM_QUANTITY_BASED_PER_ORDER');
	} else if($sm=='Price Based Per Order'){
		echo JText::_('J2STORE_SHIPM_PRICE_BASED_PER_ORDER');
	}
	?>