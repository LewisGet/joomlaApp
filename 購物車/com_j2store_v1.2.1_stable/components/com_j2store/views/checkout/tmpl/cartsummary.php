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
JHTML::_('script', 'joomla.javascript.js', 'includes/js/');
$state = @$this->state;
$order = @$this->order;
$items = @$this->orderitems;
$cart_edit_link = JRoute::_('index.php?option=com_j2store&view=mycart');

?>
<div class="j2store_cartitems">
           <table id="cart" class="" width="100%" style="clear: both;">
            <thead>
                <tr>
                    <th style="text-align: left;"><?php echo JText::_( "J2STORE_CARTSUMMARY_PRODUCTS" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "J2STORE_CARTSUMMARY_QUANTITY" ); ?></th>
                    <th style="width: 50px;"><?php echo JText::_( "J2STORE_CARTSUMMARY_TOTAL" ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php $i=0; $k=0; ?> 
            <?php foreach ($items as $item) : ?>
                <tr class="row<?php echo $k; ?>">
                    <td>
                        <a href="<?php echo JRoute::_("index.php?option=com_content&view=article&id=".$item->product_id); ?>">
                            <?php echo $item->orderitem_name; ?>
                        </a>
                        <br/>
                        
                        <?php if (!empty($item->orderitem_attribute_names)) : ?>
                            <?php echo $item->orderitem_attribute_names; ?>
                            <br/>
                        <?php endif; ?>

                            <?php echo JText::_( "J2STORE_ITEM_PRICE" ); ?>:
                            <?php echo J2StorePrices::number($item->price); ?>                         
                  
                    </td>
                    <td style="width: 50px; text-align: center;">
                        <?php echo $item->orderitem_quantity;?>  
                    </td>
                    <td style="text-align: right;">
                        <?php echo J2StorePrices::number($item->orderitem_final_price); ?>
                                               
                    </td>
                </tr>
              
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
               	<tr class="cart_subtotal">
                    <td colspan="2" style="font-weight: bold; white-space: nowrap;">
                        <?php echo JText::_( "J2STORE_CART_SUBTOTAL" ); ?>
                    </td>
                    <td colspan="3" style="text-align: right;">
                        <?php echo J2StorePrices::number($order->order_subtotal); ?>
                    </td>
                </tr>                
            </tfoot>
        </table>
        <table class="" width="100%" style="clear: both;">
                <tr>
                    <td colspan="2" style="white-space: nowrap;">
                        <b><?php echo JText::_( "J2STORE_CART_TAX_SHIPPING_TOTALS" ); ?></b>
                        <br/>
                    </td>
                    <td colspan="2" style="text-align: right;">
                    <?php 
                        	if( $order->order_tax )
		                   	{
		                   		if (!empty($this->show_tax)) { echo JText::_("J2STORE_CART_PRODUCT_TAX_INCLUDED").":<br>"; }
		                   	    else { echo JText::_("J2STORE_CART_PRODUCT_TAX").":<br>"; }    
		                   	}
		                
                    	if (!empty($this->showShipping))
                    	{
                            echo JText::_("J2STORE_CART_SHIPPING_AND_HANDLING").":<br>";                          
                    	}
                    	
                    	if (!empty($order->order_discount ))
                    	{
                            //echo JText::_("Discount")."&nbsp;(".$this->params->get('global_discount')."%) :";
                            echo "(-)";
                            echo JText::_("J2STORE_CART_DISCOUNT")." (".$this->params->get('global_discount')."%) :";
                    	}


                    ?>
                    </td>
                    <td colspan="2" style="text-align: right;">
                     <?php 
                        	if( $order->order_tax )
                            echo J2StorePrices::number($order->order_tax) . "<br>";    
                        
                        if (!empty($this->showShipping))
                        {
                            echo J2StorePrices::number($order->order_shipping) . "<br>";
                        }
                        
                        if( $order->order_discount )
                        echo J2StorePrices::number($order->order_discount)
                   
                    ?>                  
                    </td>
                </tr>
                <tr>
                	<td colspan="3" style="font-weight: bold; white-space: nowrap;">
                        <?php echo JText::_( "J2STORE_CART_GRANDTOTAL" ); ?>
                    </td>
                    <td colspan="3" style="text-align: right;">
                        <?php echo J2StorePrices::number($order->order_total); ?>
                    </td>
                </tr>                
        </table>
        <div class="edit_cart_link" style="text-align:right;padding:3px;"> 
        		<a href="<?php echo $cart_edit_link; ?>" >
				  <?php echo JText::_('J2STORE_EDIT_YOUR_CART'); ?>
				  </a>
		</div>
        <hr />        
</div>
