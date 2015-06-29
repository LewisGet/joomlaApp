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


// No direct access
defined('_JEXEC') or die;

/**
 * Submenu helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_j2store
 * @since		1.6
 */
 
class J2StoreSubmenuHelper 
{

public static function addSubmenu($vName = 'cpanel')
	{
		
		JSubMenuHelper::addEntry(
			JText::_('J2STORE_Dashboard'),
			'index.php?option=com_j2store&view=cpanel',
			$vName == 'cpanel'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('J2STORE_Orders'),
			'index.php?option=com_j2store&view=orders',
			$vName == 'orders'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('J2STORE_Tax_Profiles'),
			'index.php?option=com_j2store&view=taxprofiles',
			$vName == 'taxprofiles'
		);
		
		JSubMenuHelper::addEntry(
				JText::_('J2STORE_Shopper_Addresses'),
				'index.php?option=com_j2store&view=addresses',
				$vName == 'addresses'
		);
		
		JSubMenuHelper::addEntry(
				JText::_('J2STORE_Countries'),
				'index.php?option=com_j2store&view=countries',
				$vName == 'countries'
		);
		
		JSubMenuHelper::addEntry(
				JText::_('J2STORE_Zones'),
				'index.php?option=com_j2store&view=zones',
				$vName == 'zones'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('J2STORE_Shipping_Methods'),
			'index.php?option=com_j2store&view=shippingmethods',
			$vName == 'shippingmethods'
		);
		
			
		JSubMenuHelper::addEntry(
			JText::_('J2STORE_Information'),
			'index.php?option=com_j2store&view=info',
			$vName == 'info'
		);
		
	}
	
}	
