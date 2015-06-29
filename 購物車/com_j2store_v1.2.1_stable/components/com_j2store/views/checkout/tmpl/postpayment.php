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
	$order_link = @$this->order_link;
	$plugin_html = @$this->plugin_html;
?>

<div class='componentheading'>
    <span><?php echo JText::_( "J2STORE_CHECKOUT_RESULTS" ); ?></span>
</div>

<?php echo $plugin_html; ?>

<?php if(!empty($order_link)):?>
<div class="note">
	<a href="<?php echo JRoute::_($order_link); ?>">
        <?php echo JText::_( "J2STORE_VIEW_PRINT_INVOICE" ); ?>
	</a>
</div>
<?php endif; ?>
