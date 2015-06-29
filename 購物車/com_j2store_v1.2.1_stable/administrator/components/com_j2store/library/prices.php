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



/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
	
class J2StorePrices 
{
	
	function getPrice( $id, $quantity = '1')
	{
		// $sets[$id][$quantity][$group_id][$date]
		static $sets;
		
		if ( !is_array( $sets ) )
		{
			$sets = array( );
		}
		
		$price = null;
		if ( empty( $id ) )
		{
			return $price;
		}
		
		if ( !isset( $sets[$id][$quantity] ) )
		{
			
			( int ) $quantity;
			if ( $quantity <= '0' )
			{
				$quantity = '1';
			}
			
			// TiendaModelProductPrices is a special model that overrides getItem
			$price = J2StorePrices::getItemPrice( $id );
			$item = new JObject;
			$item->product_price = $price;
			$sets[$id][$quantity] = $item;
		}
		
		return $sets[$id][$quantity];
	}
	
		
	/**
	 * 
	 * @return unknown_type
	 */
	function getItemPrice(&$id) 
	{
		$item=null;
		$item = J2StorePrices::_getJ2Item($id);
		
		if(!empty($item))
			return $item->item_price;
		else
			return null;
	}
	
	function getItemEnabled(&$id)
	{
		$item=null;
		$item = J2StorePrices::_getJ2Item($id);
		
		if(!empty($item))
			return $item->product_enabled;
		else
			return null;
	}
	
	
	function getItemTax(&$id) {
		$item = J2StorePrices::_getJ2Item($id);

		 if ($item->item_tax) {
			 $taxrate = J2StorePrices::_getTaxRate($item->item_tax);
		//	$item_tax = $item_price * $taxrate;
		  }	else {
			  //$item_tax = 0;
			  $taxrate = 0;
		  }
		return $taxrate;
	}
	
	function _getTaxRate($taxid) {
			$db		= &JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->select('a.tax_percent');
			$query->from('`#__j2store_taxprofiles` AS a');
			$query->where('a.id='.$taxid);	
								
			$db->setQuery($query);
			return $db->loadResult();
	}
	
	function number($amount, $options='')
    {
        // default to whatever is in config
		$config = &JComponentHelper::getParams('com_j2store');
        $options = (array) $options;
        $post = '';
        $pre = '';
        
        $default_currency = $config->get('currency_code', 'USD');
        $num_decimals = isset($options['num_decimals']) ? $options['num_decimals'] : $config->get('currency_num_decimals', '2');
        $thousands = isset($options['thousands']) ? $options['thousands'] : $config->get('currency_thousands', ',');
        $decimal = isset($options['decimal']) ? $options['decimal'] : $config->get('currency_decimal', '.');
        $currency_symbol = isset($options['currency']) ? $options['currency'] : $config->get('currency', '$');
        $currency_position = isset($options['currency_position']) ? $options['currency_position'] : $config->get('currency_position', 'pre');
        if($currency_position == 'post') {
			$post = $currency_symbol;
		} else {
			$pre = $currency_symbol;
		}
		
        $return = $pre.number_format($amount, $num_decimals, $decimal, $thousands).$post;
        
        return $return;
    }
   
	function getJ2Product($id) {
			$db		= &JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			//$query->select('article_id,item_price,item_tax,item_shipping');
			$query->select('*');
			$query->from('#__j2store_prices as a');
			$query->where('a.article_id='.$id);	
								
			$db->setQuery($query);
			$item=$db->loadObject();
		return $item;
	}
	
	function _getJ2Item($id) {
			$db		= &JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			//$query->select('article_id,item_price,item_tax,item_shipping');
			$query->select('*');
			$query->from('#__j2store_prices as a');
			$query->where('a.article_id='.$id);	
								
			$db->setQuery($query);
			$item=$db->loadObject();
		return $item;
	}
	
}

