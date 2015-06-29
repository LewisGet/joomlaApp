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
jimport( 'joomla.filter.filterinput' );
jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
JLoader::register( 'J2StoreQuery', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'query.php' ); 

class J2StoreModelMyCart extends JModel {

	private $_product_id;
	
	var $_filterinput = null; // instance of JFilterInput
	
	function __construct($config = array()) 
	{
		parent::__construct($config);
		 $this->_filterinput = &JFilterInput::getInstance();       
	}

	function getAttributes($product_id) {
		$query = 'SELECT a.* FROM #__j2store_productattributes AS a WHERE a.product_id='. (int) $product_id
				 .' ORDER BY a.ordering';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
		
	}
	
	
   function getData()
	{

		static $pa, $pao;
        
        if (empty($pa)) { $pa = array(); }
        if (empty($pao)) { $pao = array(); }
        
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			 $items = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			 
			  if( empty( $items ) ){
                return array();
			   }
			   
			   
			   foreach($items as $item)
            {
            	
                // at this point, ->product_price holds the default price for the product,
                // but the user may qualify for a discount based on volume or date, so let's get that price override
                $item->product_price_override =  J2StorePrices::getPrice( $item->product_id, $item->product_qty);
                
                //checking if we do price override
                $item->product_price_override->override = true;
                
                if (!empty($item->product_price_override))
                {
                   $item->product_price = $item->product_price_override->product_price;
                }
    
 
 				$item->orderitem_attributes_price = '0.00000'; 				
 				$attributes_names = array();
 				if(!empty($item->product_attributes))
 				{
	                $item->attributes = array(); // array of each selected attribute's object	                
	                $attibutes_array = explode(',', $item->product_attributes);	                
	                foreach ($attibutes_array as $attrib_id)
	                {
	                    if (empty($pao[$attrib_id]))
	                    {
                            // load the attrib's object
                            JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'tables');
                            $pao[$attrib_id] = JTable::getInstance('ProductAttributeOptions', 'Table');
                            $pao[$attrib_id]->load( $attrib_id );	                        
	                    }
                        $table = $pao[$attrib_id];
               			
	                    // update the price
	                    // + or - 
	                    if($table->productattributeoption_prefix != '=')
	                    {
	                        $item->product_price = $item->product_price + floatval( "$table->productattributeoption_prefix"."$table->productattributeoption_price");
	                        // store the attribute's price impact	                      
	                        $item->orderitem_attributes_price = $item->orderitem_attributes_price + floatval( "$table->productattributeoption_prefix"."$table->productattributeoption_price");
							$item->product_price_override->override = true;
	                    }
	                    // only if prefix is =
	                    else 
	                    {	                   	
	                       	// assign the product attribute price as the product price
	                       	//then set the orderitem_attributes_price to 0.0000
	                        $item->product_price = $table->productattributeoption_price; //
	                        // store the attribute's price impact
	                        $item->orderitem_attributes_price = "0.00000";
	                        $item->product_price_override->override = false;
	                    }
                 
	                    $item->orderitem_attributes_price = number_format($item->orderitem_attributes_price, '5', '.', '');
	                   // $item->product_sku .= $table->productattributeoption_code;
	                    
	                    // store a csv of the attrib names, built by Attribute name + Attribute option name
	                    if (empty($pa[$table->productattribute_id]))
                        {
                            $pa[$table->productattribute_id] = JTable::getInstance('ProductAttributes', 'Table');
                            $pa[$table->productattribute_id]->load( $table->productattribute_id );
                        }
                        $atable = $pa[$table->productattribute_id];
                        
	                    if (!empty($atable->productattribute_id))
	                    {
	                        $name = JText::_($atable->productattribute_name) . ': ' . JText::_( $table->productattributeoption_name );
	                        $attributes_names[] = $name;
	                    } 
	                        else
	                    {
	                        $attributes_names[] = JText::_( $table->productattributeoption_name );
	                    }
	                }
    		
	                 
	                // Could someone explain to me why this is necessary?
	                if ($item->orderitem_attributes_price >= 0)
	                {
	                    // formatted for storage in the DB
	                    $item->orderitem_attributes_price = "+$item->orderitem_attributes_price";
	                }	
 				}

 				$item->attributes_names = implode(', ', $attributes_names);
            }
			   
			$this->_data = $items;	 			 
		}
		
		
		return $this->_data;
	}
	
    
     protected function _buildQuery( $refresh=false )
    {
    	//if (!empty($this->_query) && !$refresh)
    	//{
    	//	return $this->_query;
    	//}

    	$query = new J2StoreQuery();

        $this->_buildQueryFields($query);
        $this->_buildQueryJoins($query);
        $this->_buildQueryWhere($query);
      
		return $query;
    }
    
    protected function _buildQueryWhere(&$query)
    {
		
			$filter_user     = $this->getState('filter_user');
			$filter_session  = $this->getState('filter_session');
			$filter_name  = $this->getState('filter_name');
        	$query->where('p.state=1');
        	
        if (strlen($filter_user))
        {
            $query->where('tbl.user_id = '.$this->_db->Quote($filter_user));
        }

        if (strlen($filter_session))
        {
            $query->where( "tbl.session_id = ".$this->_db->Quote($filter_session));
        }

        if (!empty($filter_product))
        {
            $query->where('tbl.product_id = '.(int) $filter_product);
            $this->setState('limit', 1);
       	}

       	if (strlen($filter_name))
        {
        	$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_name ) ) ).'%');
        	$query->where('LOWER(p.title) LIKE '.$key);
       	}
    }

    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__content AS p ON tbl.product_id = p.id');
	}

	protected function _buildQueryFields(&$query)
	{
       	$field = array();
        $field[] = " p.title as product_name";
		// This subquery returns the default price for the product and allows for sorting by price
		$date = JFactory::getDate()->toMysql();
	
        $query->select( 'tbl.*');
        $query->from('#__j2store_mycart AS tbl');
        $query->select( $field );
	}
	
	
	 public function getShippingIsEnabled()
    {
	   	$model = JModel::getInstance( 'MyCart', 'J2StoreModel');

        $session =& JFactory::getSession();
        $user =& JFactory::getUser();
        $model->setState('filter_user', $user->id );
        if (empty($user->id))
        {
            $model->setState('filter_session', $session->getId() );
        }

		$list = $model->getData();

    	// If no item in the list, return false
        if ( empty( $list ) )
        {
          	return false;
        }
        
        require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'j2item.php');
        $product_helper = new J2StoreItem();        
        foreach ($list as $item)
        {
           	$shipping = $product_helper->isShippingEnabled($item->product_id);
        	if ($shipping)
        	{
        	    return true;
        	}
        }
        
        return false;
    }
    
}
