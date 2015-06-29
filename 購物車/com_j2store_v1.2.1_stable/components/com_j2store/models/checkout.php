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

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

class J2StoreModelCheckout extends JModel {
	
	 function getData($ordering = NULL) {
		 
	 }
	 
	 
	 function checkBillingAddress() {
	 	$user =	& JFactory::getUser();
	 	$db = &JFactory::getDBO();
	 	$session = JFactory::getSession();
	 	$mail = $session->get('guest_mail');
	 	
	 	if(empty($mail) && $user->id)
	 		$query = "SELECT * FROM #__j2store_address WHERE user_id={$user->id} AND type='billing'";
	 	elseif(!empty($mail))
	 		$query = "SELECT * FROM #__j2store_address WHERE email=".$db->quote($mail)." AND type='billing' ORDER BY id DESC";
	 	$db->setQuery($query);
	 	return $db->loadObject();
	 	
	 }
	  
	 function checkShippingAddress() {
		$user =	& JFactory::getUser();
		$db = &JFactory::getDBO();
		$session = JFactory::getSession();
		$mail = $session->get('guest_mail');
		
		if(empty($mail) && $user->id)
			$query = "SELECT * FROM #__j2store_address WHERE user_id={$user->id}  AND type='shipping'";
		elseif(!empty($mail))
			$query = "SELECT * FROM #__j2store_address WHERE email=".$db->quote($mail)." AND type='shipping' ORDER BY id DESC";
		$db->setQuery($query);
		return $db->loadObject();
	} 
	 
	function getCountryList($name,$field_id,$default_cid)
	{
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('a.country_id,a.country_name');
		$query->from('#__j2store_countries AS a');
		$query->where('state = 1');
		$query->order('a.country_name');
		$db->setQuery($query);
		$countries =$db->loadObjectList();
		$params = &JComponentHelper::getParams('com_j2store');
		
		//generate country filter list
		$country_options = array();
		$country_options[] = JHTML::_('select.option', '', JText::_('J2STORE_SELECT_COUNTRY'));
		foreach($countries as $row) {
			$country_options[] =  JHTML::_('select.option', $row->country_id, $row->country_name);
		}
		
		$req = ($params->get('bill_country_zone')==1)?'required':'';
		
		//check for adding required class
		if($field_id == 'billing:country') { $class = 'class="'.$req.'"'; } else { $class=''; };
		
	
		return JHTML::_('select.genericlist', $country_options, $name, $class, 'value', 'text', $default_cid, $field_id);
	
	}
	
	
	function getZoneList($name,$id,$country_id,$zid)
	{
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('a.zone_id,a.zone_name');
		$query->from('#__j2store_zones AS a');
		$query->where('state = 1 AND country_id='.$country_id);
		$query->order('a.zone_name');
		$db->setQuery($query);
		$zones =$db->loadObjectList();
		//generate country filter list
		$zone_options = array();
		$zone_options[] = JHTML::_('select.option', '', JText::_('J2STORE_SELECT_STATE'));
		foreach($zones as $row) {
			$zone_options[] =  JHTML::_('select.option', $row->zone_id, $row->zone_name);
		}
	
		return JHTML::_('select.genericlist', $zone_options, $name, '', 'value', 'text',$zid,$id);
	}

}
