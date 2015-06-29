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
$items = @$this->cartobj->items;
$subtotal = @$this->cartobj->subtotal;
$state = @$this->state;
$quantities = array();
$action = JRoute::_('index.php?option=com_j2store&view=mycart' );
$checkout_url = JRoute::_('index.php?option=com_j2store&view=checkout');
$remove_image_url = JURI::root(true).'/components/com_j2store/';
?>
<script type="text/javascript">
var options = {size: {x: <?php echo $this->params->get('popup_size_width', 600);?>, y: <?php echo $this->params->get('popup_size_height', 450);?>}};
SqueezeBox.setOptions(options);
</script>
<div id="j2storeCartPopup">

<div class='componentheading'>
    <span><?php echo JText::_( "J2STORE_MY_SHOPPING_CART" ); ?></span>
</div>

<div class="j2store_cartitems">
    <?php if (!empty($items)) { ?>
    <form action="<?php echo $action; ?>" method="post" name="adminForm" enctype="multipart/form-data">

        <table id="cart" class="" style="clear: both;" width="100%">
            <thead>
                <tr>
					<?php if($this->params->get('show_thumb_cart')) : ?>
					<th style=""><?php echo JText::_( "J2STORE_CART_ITEM" ); ?></th>
                    <?php endif; ?>
                    <th style=""><?php echo JText::_( "J2STORE_CART_ITEM_DESC" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "J2STORE_CART_ITEM_QUANTITY" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "J2STORE_CART_ITEM_TOTAL" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "J2STORE_CART_ITEM_REMOVE" ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php $i=0; $k=0; $subtotal = 0;?>
            <?php foreach ($items as $item) : ?>

            	<?php
            		$link = "index.php?option=com_content&view=article&id=".$item->product_id;
            		$link = JRoute::_($link);
            		$image = J2StoreItem::getJ2Image($item->product_id, $this->params);
            	?>

                <tr class="row<?php echo $k; ?>">
                    <?php if($this->params->get('show_thumb_cart')) : ?>
                    <td style="text-align: center;">
                        <?php if(!empty($image)) {echo $image; }?>
                    </td>
                    <?php endif; ?>
                    <td>
                      	<strong><?php echo $item->product_name; ?></strong>
                        <br/>
                        <?php if (!empty($item->attributes_names)) : ?>
	                        <?php echo $item->attributes_names; ?>
	                        <br/>
	                    <?php endif; ?>
	                    <?php if (!empty($item->product_sku)) : ?>
                            <b><?php echo JText::_( "J2STORE_SKU" ); ?>:</b>
                            <?php echo $item->product_sku; ?>
                            <br/>
                        <?php endif; ?>

                         <?php echo JText::_( "J2STORE_ITEM_PRICE" ); ?>: <?php echo J2StorePrices::number($item->product_price); ?>

                    </td>
                    <td style="width: 50px; text-align: center;">
                        <?php echo $item->product_qty; ?>
                    </td>
                    <td style="text-align: right;">
                        <?php $subtotal = $subtotal + $item->subtotal; ?>
                        <?php echo J2StorePrices::number($item->subtotal); ?>
                    </td>
                    <td><a title="<?php echo JText::_( 'J2STORE_CART_REMOVE_ITEM' ); ?>" onclick="j2storeCartRemove(this, <?php echo $item->cart_id; ?>, <?php echo $item->product_id; ?>, 1)"> <div class="j2storeCartRemove"> </div>  </a>  </td>
                </tr>
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
				<tr class="cart_subtotal">
                    <td colspan="3" style="font-weight: bold;">
                        <?php echo JText::_( "J2STORE_CART_SUBTOTAL" ); ?>
                    </td>
                    <td colspan="1" style="text-align: right;">
                        <?php echo J2StorePrices::number($subtotal); ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>

                <tr>
                    <td colspan="2" style="text-align: left;">
                        <input type="button" class="button" value="<?php echo JText::_('J2STORE_CHECKOUT'); ?>" onclick="SqueezeBox.close(); window.location = '<?php echo $checkout_url; ?>';" />
                    </td>
                    <td colspan="2" style="text-align: left;">
                        <input type="button" class="button" value="<?php echo JText::_('J2STORE_EDIT_CART'); ?>" onclick="window.location = '<?php echo $action; ?>';" />
                    </td>
                    <td colspan="1">
                        <input type="button" class="button" value="<?php echo JText::_('J2STORE_CONTINUE_SHOPPING'); ?>" onclick="SqueezeBox.close();" />
                    </td>
                </tr>

                <tr>
                	<td colspan="5" style="white-space: nowrap;">
                        <b><?php echo JText::_( "J2STORE_CART_TAX_SHIPPING_TOTALS" ); ?></b>
                        <br/>
                        <?php
                            echo JText::_('J2STORE_CALCULATED_DURING_CHECKOUT_PROCESS');
                    	?>
              	 	</td>
                </tr>
            </tfoot>
        </table>

        <input type="hidden" name="boxchecked" value="" />
    </form>
    <?php } else { ?>
    <p><?php echo JText::_( 'J2STORE_CART_EMPTY' ); ?></p>
    <?php } ?>
</div>
</div>
