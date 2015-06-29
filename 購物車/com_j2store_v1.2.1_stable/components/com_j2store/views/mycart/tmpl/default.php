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
JHTML::_('script', 'joomla.javascript.js', 'includes/js/');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'j2item.php');
$items = @$this->cartobj->items;
$subtotal = @$this->cartobj->subtotal;
$state = @$this->state;
$quantities = array();
$action = JRoute::_('index.php?option=com_j2store&view=mycart&task=update');
$checkout_url = JRoute::_('index.php?option=com_j2store&view=checkout');
?>

<?php //if(!$this->popup):
	//if(isset($this->popup)):?>
<div id="j2storeCartPopup">
<?php // endif; ?>

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
					<th style="text-align: left;"><?php echo JText::_( "J2STORE_CART_ITEM" ); ?></th>
                    <?php endif; ?>
                    <th style="text-align: left;"><?php echo JText::_( "J2STORE_CART_ITEM_DESC" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "J2STORE_CART_ITEM_QUANTITY" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "J2STORE_CART_ITEM_TOTAL" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "J2STORE_CART_ITEM_REMOVE" ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php $i=0; $k=0; $subtotal = 0;?>
            <?php foreach ($items as $item) : ?>

            	<?php
            	//	$product_params = new JParameter( trim(@$item->cartitem_params) );
            	//	$link = $product_params->get('product_url', "index.php?option=com_k2&view=item&id=".$item->product_id);
					$link = JRoute::_("index.php?option=com_content&view=article&id=".$item->product_id);
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
                        <a href="<?php echo $link; ?>">
                            <?php echo $item->product_name; ?>
                        </a>
                        <br/>

                        <?php if (!empty($item->attributes_names)) : ?>
	                        <?php echo $item->attributes_names; ?>
	                        <br/>
	                    <?php endif; ?>
	                    <input name="product_attributes[<?php echo $item->cart_id; ?>]" value="<?php echo $item->product_attributes; ?>" type="hidden" />

                         <?php echo JText::_( "J2STORE_ITEM_PRICE" ); ?>: <?php echo J2StorePrices::number($item->product_price); ?>

                    </td>
                    <td style="width: 50px; text-align: center;">
                        <?php $type = 'text';
                       ?>

                        <input name="quantities[<?php echo $item->cart_id; ?>]" type="<?php echo $type; ?>" size="3" maxlength="3" value="<?php echo $item->product_qty; ?>" />

                        <!-- Keep Original quantity to check any update to it when going to checkout -->
                        <input name="original_quantities[<?php echo $item->cart_id; ?>]" type="hidden" value="<?php echo $item->product_qty; ?>" />
                    </td>
                    <td style="text-align: right;">
                        <?php $subtotal = $subtotal + $item->subtotal; ?>
                        <?php echo J2StorePrices::number($item->subtotal); ?>
                    </td>
                    <td><a href="#" title="<?php echo JText::_( 'J2STORE_CART_REMOVE_ITEM' ); ?>" onclick="j2storeCartRemove(this, <?php echo $item->cart_id; ?>, <?php echo $item->product_id; ?>, 2)"> 
                    <div class="j2storeCartRemove"> </div> 
                    </a>  </td>
                </tr>
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
               	<tr class="cart_subtotal">
                    <td colspan="<?php echo $colspan=($this->params->get('show_thumb_cart'))? 3:2 ?>" style="font-weight: bold;">
                        <?php echo JText::_( "J2STORE_CART_SUBTOTAL" ); ?>
                    </td>
                    <td colspan="1" style="text-align: right;">
                        <?php echo J2StorePrices::number($subtotal); ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
        <table id="cart_actions" width="100%">

               <tr>
                    <td colspan="5">
                        <input style="float: right;" type="submit" class="button" value="<?php echo JText::_('J2STORE_UPDATE_QUANTITIES'); ?>" name="update" />
                    </td>
                </tr>

                <tr>
                	<td colspan="5" style="white-space: nowrap;">
                        <b><?php echo JText::_( "J2STORE_CART_TAX_SHIPPING_TOTALS" ); ?></b>
                        <br/>
                        <?php
                            echo JText::_( "J2STORE_CALCULATED_DURING_CHECKOUT_PROCESS" );
                    	?>
              	 	</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <?php if (!empty($this->return)) { ?>
                        [<a href="<?php echo $this->return; ?>">
                            <?php echo JText::_( "J2STORE_CONTINUE_SHOPPING" ); ?>
                        </a>]
                        <?php } ?>
                    </td>
                    <td style="text-align: right;" nowrap>
				        <div style="float: right;">
				        <a class="begin_checkout" href="<?php echo $checkout_url; ?>">
				            <?php echo JText::_( "J2STORE_BEGIN_CHECKOUT" ); ?>
				        </a>
				        </div>
                    </td>
                </tr>

        </table>

        <input type="hidden" name="boxchecked" value="" />
    </form>
    <?php } else { ?>
    <p><?php echo JText::_( "J2STORE_NO_ITEMS" ); ?></p>
    <?php } ?>
</div>
<?php //if(!$this->popup):
	//if(isset($this->popup)):?>
</div>
<?php //endif; ?>
