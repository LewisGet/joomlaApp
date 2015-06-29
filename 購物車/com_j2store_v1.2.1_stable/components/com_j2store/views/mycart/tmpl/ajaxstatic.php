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
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'j2item.php');
$action = JRoute::_('index.php?option=com_j2store&view=mycart' );
$checkout_url = JRoute::_('index.php?option=com_j2store&view=checkout');
//get the last product added
$items = @$this->cartobj->items;
$item_count = count($items);
$product_name = $items[$item_count-1]->product_name;

?>
<script type="text/javascript">
var options = {size: {x: <?php echo $this->params->get('popup_size_width', 200);?>, y: <?php echo $this->params->get('popup_size_height', 150);?>}};
SqueezeBox.setOptions(options);
</script>
<div class='componentheading'>
	<span><?php echo JText::_( "J2STORE_MY_SHOPPING_CART" ); ?> </span>
</div>

<?php if($item_count):?>

<div class="j2store_minicart_product_header">
		<?php echo JText::_('J2STORE_MINICART_PRODUCT_ADDED'); ?>
	</div>

<div class="j2store_addtocart_mini">
	<div class="j2store_minicart_section_left" id="j2store_minicart_title_image">


		<span class="j2store_minicart_product_name"> <b><?php echo $product_name; ?>
		</b>
		<br/>
		</span>

		<?php if($this->params->get('show_thumb_cart')) :
		$link = "index.php?option=com_content&view=article&id=". $items[$item_count-1]->product_id;
		$link = JRoute::_($link);
		$image = J2StoreItem::getJ2Image( $items[$item_count-1]->product_id, $this->params);
		?>
		<span class="j2store_minicart_image"> <?php if(!empty($image)) {
			echo $image;
		}
		?>
		</span>
		<?php endif; ?>

	</div>
	<div class="j2store_minicart_section_right">

		<ul>
			<li><input type="button" class="button"
				value="<?php echo JText::_('J2STORE_CHECKOUT'); ?>"
				onclick="SqueezeBox.close(); window.location = '<?php echo $checkout_url; ?>';" />
			</li>
			<li><input type="button" class="button"
				value="<?php echo JText::_('J2STORE_EDIT_CART'); ?>"
				onclick="window.location = '<?php echo $action; ?>';" /></li>
			<li><input type="button" class="button"
				value="<?php echo JText::_('J2STORE_CONTINUE_SHOPPING'); ?>"
				onclick="SqueezeBox.close();" /></li>
		</ul>
	</div>
</div>
<?php else: ?>
<p>
	<?php echo JText::_( 'J2STORE_CART_EMPTY' ); ?>
</p>
<?php endif; ?>
