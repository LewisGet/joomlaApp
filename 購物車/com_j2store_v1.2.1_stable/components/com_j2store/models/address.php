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

class J2StoreModelAddress extends JModel {

public function getList()
    {
        $list = parent::getList();
        
        // If no item in the list, return an array()
        if( empty( $list ) ){
        	return array();
        }
        
        foreach($list as $item)
        {
            $item->link = 'index.php?option=com_j2store&view=addresses&task=edit&id='.$item->address_id;
        }
        return $list;
    }
    
    
    public function getShippingAddress() {
		 
		$user =	& JFactory::getUser();
		$db = &JFactory::getDBO();
		
		$query = "SELECT * FROM #__j2store_address WHERE user_id={$user->id}";
		$db->setQuery($query);
		return $db->loadObject();
		 
	 } 
    
   public function saveBillingAddress($post, $id='') {
	   
		$db = &JFactory::getDBO();
        $user = &JFactory::getUser();
        
        $row = &JTable::getInstance('Address', 'Table');
      
        //set the id so that it updates the record rather than changing
        if($id) {
        	$post['id'] = $id;
        }
        
		if (!$row->bind($post)) {
           $this->setError($row->getError());
           return false;
        }
      	
        if($user->id) {
        $row->user_id = $user->id;
        }
        
        if($guest_mail = JFactory::getSession()->get('guest_mail')) {
        	$row->email = $guest_mail;
        }
        
        $row->type = 'billing';
        
        if (!$row->check()) {
            $this->setError($row->getError());
            return false;
        }
        
        if (!$row->store()) {
            $this->setError($row->getError());
            return false;
        }
       return $row->id;
	   
   }
   
   public function saveShippingAddress($post, $id='') {
   
   	$db = &JFactory::getDBO();
   	$user = &JFactory::getUser();
   
   	$row = &JTable::getInstance('Address', 'Table');
   
   	//set the id so that it updates the record rather than changing
   	if($id) {
   		$post['id'] = $id;
   	}
   
   	if (!$row->bind($post)) {
   		$this->setError($row->getError());
   		return false;
   	}
   
   		if($user->id) {
        $row->user_id = $user->id;
        }
        
        if($guest_mail = JFactory::getSession()->get('guest_mail')) {
        	$row->email = $guest_mail;
        }
        
   	$row->type = 'shipping';
   
   	if (!$row->check()) {
   		$this->setError($row->getError());
   		return false;
   	}
   
   	if (!$row->store()) {
   		$this->setError($row->getError());
   		return false;
   	}
   	return $row->id;
   
   }
   
   function getAddress($address_id) {
   	
   	$db = &JFactory::getDBO();
   	$query = 'SELECT tbl.*,c.country_name,z.zone_name'
   			.' FROM #__j2store_address AS tbl'	
   			.' LEFT JOIN #__j2store_countries AS c ON tbl.country_id=c.country_id'
   			.' LEFT JOIN #__j2store_zones AS z ON tbl.zone_id=z.zone_id'
   			.' WHERE tbl.id='.(int) $address_id;
   	$db->setQuery($query);
   	return $db->loadAssoc();   
   }
    
   
}
