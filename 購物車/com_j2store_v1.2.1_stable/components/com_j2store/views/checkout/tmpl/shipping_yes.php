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



defined('_JEXEC') or die('Restricted access'); 
?>

<?php
    if (!empty($this->shipping_name)) 
    {  
	?>    
       <div class="shippingName">
       <?php echo JText::_('J2STORE_STANDARD_SHIPPING_METHODStandard Shipping Method'); ?>       
       [<?php echo $this->shipping_name; ?>]
       </div>
    <?php
    }
        else
    {
        ?>
        <div class="note">
        <?php echo JText::_( "J2STORE_NO_SHIPPING_RATES_FOUND" ); ?>
        </div>
        <?php
    }
?>
