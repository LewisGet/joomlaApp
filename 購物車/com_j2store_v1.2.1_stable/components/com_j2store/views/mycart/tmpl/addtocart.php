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



$item = @$this->item;
$formName = 'j2storeadminForm_'.$item->product_id;
require_once (JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'helpers'.DS.'cart.php');
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'select.php');
$action = JRoute::_('index.php?option=com_j2store&view=mycart');
//$action = JRoute::_('index.php?option=com_j2store&view=mycart&Itemid='.$item->product_id);

?>

<form action="<?php echo $action; ?>" method="post" class="j2storeadminform" id="<?php echo $formName; ?>" name="<?php echo $formName; ?>" enctype="multipart/form-data" >

	<!--base price-->
    <span id="product_price_<?php echo $item->product_id; ?>" class="product_price">
    	<?php  echo J2StoreHelperCart::dispayPriceWithTax($item->price, $item->tax, $this->params->get('price_display_options', 1)); ?>
    </span>

  <!--attribute options-->
    <div id='product_attributeoptions_<?php echo $item->product_id; ?>' class="product_attributeoptions">
    <?php
    $default = J2StoreHelperCart::getDefaultAttributeOptions($this->attributes);

    foreach ($this->attributes as $attribute)
    {
        ?>
        <div class="pao" id='productattributeoption_<?php echo $attribute->productattribute_id; ?>'>
        <?php
        echo "<span>".$attribute->productattribute_name." : </span>";

        $key = 'attribute_'.$attribute->productattribute_id;
        $selected = (!empty($values[$key])) ? $values[$key] : $default[$attribute->productattribute_id];

         // Selected attribute options (for child attributes)
		$selected_opts = (!empty($this->selected_opts)) ? json_decode($this->selected_opts) : 0;

		if(!count($selected_opts))
		{
			$selected_opts = 0;
		}
        $attribs = array('class' => 'inputbox', 'size' => '1');
        echo J2StoreSelect::productattributeoptions( $attribute->productattribute_id, $selected, $key, $attribs, null, $selected_opts  );

        ?>

        </div>
        <?php
    }
    ?>

    </div>

     <div id='product_quantity_input_<?php echo $item->product_id; ?>' class="product_quantity_input">
		<span class="title"><?php echo JText::_( "J2STORE_ADDTOCART_QUANTITY" ); ?>:</span>
		<input type="text" name="product_qty" value="<?php echo $item->product_quantity; ?>" size="2" />

     </div>


      <!-- Add to cart button -->
    <div id='add_to_cart_<?php echo $item->product_id; ?>' class="add_to_cart" style="display: block;">
        <input type="hidden" name="product_id" value="<?php echo $item->product_id; ?>" />
        <input type="hidden" id="task" name="task" value="" />
        <?php echo JHTML::_( 'form.token' ); ?>
       <!--  <input type="hidden" name="return" value="<?php echo base64_encode( JUri::getInstance()->toString() ); ?>" />  -->

       <?php if($this->params->get('popup_style') == 3):?>
       <input value="<?php echo JText::_('J2STORE_ADD_TO_CART'); ?>" type="submit" class="addcart button" />
       <?php else: ?>
		<?php
		$onclick = "j2storeAddToCart( 'index.php?option=com_j2store&view=mycart&Itemid=1', 'addtocart', document.".$formName.", true, '".JText::_( 'J2STORE_PROCESSING' )."' );";
		//$onclick = "j2storeAddToCart( 'index.php?option=com_j2store&view=mycart&Itemid=1', 'addtocart', document.".$formName.", true, '".JText::_( 'Processing' )."' );";
		//$onclick = "document.".$formName.".submit();";
		?>
     	<input onclick="<?php echo $onclick; ?>" value="<?php echo JText::_('J2STORE_ADD_TO_CART'); ?>" type="button" class="addcart button" />
     <?php endif; ?>
    </div>

</form>
