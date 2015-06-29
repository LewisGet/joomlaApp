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
?>
<div style="float:left;">
    <div class="icon">
	    <a rel="{handler: 'iframe', size: {x: 850, y: 500}, onClose: function() {}}"
	 href="index.php?option=com_config&view=component&component=com_j2store&tmpl=component" class="modal">
	     <img alt="<?php echo JText::_('J2STORE_OPTIONS'); ?>" src="components/com_j2store/images/dashboard/config.png" />
		    <span><?php echo JText::_('J2STORE_OPTIONS'); ?></span>
	    </a>
    </div>
  </div>  
  <div style="float:left;">
    <div class="icon">
	    <a href="index.php?option=com_j2store&amp;view=orders">
		    <img alt="<?php echo JText::_('J2STORE_ORDERS'); ?>" src="components/com_j2store/images/dashboard/orders.png" />
		    <span><?php echo JText::_('J2STORE_ORDERS'); ?></span>
	    </a>
    </div>
  </div>  
  <div style="float:left;">
    <div class="icon">
	    <a href="index.php?option=com_j2store&amp;view=taxprofiles">
		    <img alt="<?php echo JText::_('J2STORE_TAX_PROFILES'); ?>" src="components/com_j2store/images/dashboard/taxprofiles.png" />
		    <span><?php echo JText::_('J2STORE_TAX_PROFILES'); ?></span>
	    </a>
    </div>
  </div>

  <div style="float:left;">
    <div class="icon">
	    <a href="index.php?option=com_j2store&amp;view=addresses">
		    <img alt="<?php echo JText::_('J2STORE_ADDRESS'); ?>" src="components/com_j2store/images/dashboard/shoppers_address.png" />
		    <span><?php echo JText::_('J2STORE_ADDRESS'); ?></span>
	    </a>
    </div>
  </div>

  <div style="float:left;">
    <div class="icon">
	    <a href="index.php?option=com_j2store&view=countries">
		    <img alt="<?php echo JText::_('J2STORE_COUNTRIES'); ?>" src="components/com_j2store/images/dashboard/countries.png" />
		    <span><?php echo JText::_('J2STORE_COUNTRIES'); ?></span>
	    </a>
    </div>
  </div>
  
  <div style="float:left;">
    <div class="icon">
	    <a href="index.php?option=com_j2store&view=zones">
		    <img alt="<?php echo JText::_('J2STORE_ZONES'); ?>" src="components/com_j2store/images/dashboard/zones.png" />
		    <span><?php echo JText::_('J2STORE_ZONES'); ?></span>
	    </a>
    </div>
  </div>  
  <div style="float:left;">
    <div class="icon">
	    <a href="index.php?option=com_j2store&amp;view=shippingmethods">
		    <img alt="<?php echo JText::_('J2STORE_SHIPPING_METHODS'); ?>" src="components/com_j2store/images/dashboard/shipping_methods.png" />
		    <span><?php echo JText::_('J2STORE_SHIPPING_METHODS'); ?></span>
	    </a>
    </div>
  </div>
  

  <div style="float:left;">
    <div class="icon">
	    <a class="modal" rel="{handler: 'iframe', size: {x: 1040, y: 600}}" href="http://j2store.org" title="<?php echo JText::_('J2STORE_NEWS_ON_J2STORE'); ?>">
		    <img alt="<?php echo JText::_('J2STORE_NEWS_ON_J2STORE'); ?>" src="components/com_j2store/images/dashboard/info.png" />
		    <span><?php echo JText::_('J2STORE_INFO'); ?></span>
	    </a>
    </div>
  </div>
  
