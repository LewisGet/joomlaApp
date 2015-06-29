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
$link = JRoute::_('index.php?option=com_j2store&view=mycart');
$items = @$this->cartobj->items;
$subtotal = @$this->cartobj->subtotal;

?>
	<div class="minicart">
	
	<?php 
	$i = 0; $subtotal = 0; $qtytotal = 0; 
	if($items) {
		foreach ($items as $item) : 
			$subtotal = $subtotal + $item->subtotal;
			$qtytotal = $qtytotal + $item->product_qty;
			$i++;
		endforeach;
	}	
	?>
	  <p><?php echo count($items).JText::_('J2STORE_ITEMS_IN_CART'); ?>	</p>
	</div>
	
		<div class="miniCartButton">
			<a href="<?php echo $link; ?>"><?php echo JText::_('J2STORE_VIEW_CART'); ?></a>
		</div>
		
