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
	$link = JRoute::_('index.php?option=com_j2store&view=checkout');
?>

<div id="container">
			<h1><?php echo $shopname; ?> - <?php echo JText::_('J2STORE_SHOPPING_CART'); ?></h1>
			<?php if ( $this->items) : ?>
			<form action="index.php?option=com_j2store" method="get" name="userForm" id="userForm">
				<table id="cart">
					<tr>
						<th><?php echo JText::_('J2STORE_CART_ITEM_QUANTITY'); ?></th>
						<th><?php echo JText::_('J2STORE_CART_ITEM_NAME'); ?></th>
						<th><?php echo JText::_('J2STORE_CART_ITEM_ID'); ?></th>
						<th><?php echo JText::_('J2STORE_CART_ITEM_UNIT_PRICE'); ?></th>
						<th><?php echo JText::_('J2STORE_CART_ITEM_TOTAL'); ?></th>
					</tr>
					<?php
						$total_price = $i = 0;
						foreach ( $this->items as $order_code=>$quantity ) :
							$total_price += $quantity*$this->model->getItemPrice($order_code);
					?>
						<?php echo $i++%2==0 ? "<tr>" : "<tr class='odd'>"; ?>
							<td class="quantity center"><?php echo $quantity; ?></td>
							<td class="item_name"><?php echo $this->model->getItemName($order_code); ?></td>
							<td class="order_code"><?php echo $order_code; ?></td>
							<td class="unit_price"><?php echo $currency;?><?php echo $this->model->getItemPrice($order_code); ?></td>
							<td class="extended_price"><?php echo $currency;?><?php echo ($this->model->getItemPrice($order_code)*$quantity); ?></td>
						</tr>
					<?php endforeach; ?>
					<tr><td align="right" colspan="4"><?php echo JText::_('TOTAL'); ?> : &nbsp;&nbsp;</td><td id="total_price"><?php echo $currency;?><?php echo $total_price; ?></td></tr>
				</table>
				<table>
					<tr>
					<td><input type="button" value="<?php echo JText::_('CHECKOUT'); ?>" onclick="SqueezeBox.close(); window.location = '<?php echo $link; ?>';" />
					<td><input type="button" value="<?php echo JText::_('CONTINUE SHOPPING'); ?>" onclick="SqueezeBox.close();" />
					</tr>
				</table>
				<input type="hidden" name="option" value="com_j2store" />
				<input type="hidden" name="view" value="checkout" />
			</form>
			<?php else: ?>
				<p class="center"><?php echo JText::_('NO ITEMS'); ?></p>
			<?php endif; ?>
		</div>
