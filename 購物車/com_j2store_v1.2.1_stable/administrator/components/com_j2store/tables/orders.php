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



// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
JLoader::register( 'J2StoreTable', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'tables'.DS.'_base.php' ); 

class TableOrders extends J2StoreTable
{
	
	 /** @var array An array of TiendaTableOrderItems objects */
    protected $_items = array();
   
    /** @var array      The shipping totals JObjects */
    protected $_shipping_totals = array();
    
    /** @var array An array of TiendaTableTaxRates objects (the unique taxrates for this order) */
    protected $_taxrates = array();
    
    /** @var array An array of tax amounts, indexed by tax_rate_id */
    protected $_taxrate_amounts = array();
 		
	/**
	* @param database A database connector object
	*/
	 function TableOrders ( &$db ) 
	{
		parent::__construct('#__j2store_orders', 'id', $db );
	}
	
		/**
	 * Loads the Order object with values from the DB tables
	 */
    function load( $oid=null, $reset=true )
    {
    	if ($return = parent::load($oid, $reset))
    	{
    		// TODO populate the protected vars with the info from the db
    	}
    	return $return;
    }
		
	function check()
	{
        $db         = $this->getDBO();
        $nullDate   = $db->getNullDate();
	    if (empty($this->created_date) || $this->created_date == $nullDate)
        {
            $date = JFactory::getDate();
            $this->created_date = $date->toMysql();
        }
		return true;
	}
	
	
	 function addItem( $item )
    {
        $orderItem = JTable::getInstance('OrderItems', 'Table');
        if (is_array($item))
        {
            $orderItem->bind( $item );
        }
        elseif (is_object($item) && is_a($item, 'TableOrderItems'))
        {
            $orderItem = $item;
        }
        elseif (is_object($item))
        {
            $orderItem->product_id = @$item->product_id;
            $orderItem->orderitem_quantity = @$item->orderitem_quantity;
            $orderItem->orderitem_attributes = @$item->orderitem_attributes;
        }
        else
        {
            $orderItem->product_id = $item;
            $orderItem->orderitem_quantity = '1';
            $orderItem->orderitem_attributes = '';
        }
        
        // Use hash to separate items when customer is buying the same product from multiple vendors
        // and with different attribs
			$hash = intval($orderItem->product_id).".".$orderItem->orderitem_attributes;
  
        if (!empty($this->_items[$hash]))
        {
            // merely update quantity if item already in list
            $this->_items[$hash]->orderitem_quantity += $orderItem->orderitem_quantity;
        }
            else
        {
            $this->_items[$hash] = $orderItem; 
        }        
      
    }
    
    
    function calculateTotals()
    {
        // get the subtotal first. 
        // if there are per_product coupons and coupons_before_tax, the orderitem_final_price will be adjusted
        // and ordercoupons created
        $this->calculateProductTotals();
         
        // then calculate the tax
        $this->calculateTaxTotals(); 
        
       // then calculate shipping total
        $this->calculateShippingTotals(); 
        
        // then calculate shipping total
        $this->calculateDiscountTotals(); 
       
        // sum totals
        $total = 
            $this->order_subtotal 
            + $this->order_tax 
            + $this->order_shipping 
          //  + $this->order_shipping_tax
            - $this->order_discount
            ;
        
        // set object properties
		$this->order_total      = $total;       
    }
    
    
     /**
     * Calculates the product total (aka subtotal) 
     * using the array of items in the order object
     * 
     * @return unknown_type
     */
    function calculateProductTotals()
    {
        $subtotal = 0.00;
        
        // TODO Must decide what we want these methods to return; for now, null
        $items = &$this->getItems();      
        if (!is_array($items))
        {
            $this->order_subtotal = $subtotal;
            return;
        }
        
        // calculate product subtotal
        foreach ($items as $item)
        {
			
			//$item->orderitem_final_price;
		    // track the subtotal
            $subtotal += $item->orderitem_final_price;
        }

        // set object properties
        $this->order_subtotal   = $subtotal;
        
    }
    
    
      /**
     * Calculates the tax totals for the order
     * using the array of items in the order object
     * 
     * @return unknown_type
     */
    function calculateTaxTotals()
    {
		
	    require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'prices.php'); 
        $tax_total = 0.00;
        
        $items =& $this->getItems();
        if (!is_array($items))
        {
            $this->order_tax = $tax_total;
            return;
        }
        
        foreach ($items as $key=>$item)
        {
            $orderitem_tax = 0;
            
            $product_tax_rate = J2StorePrices::getItemTax($item->product_id);
            // track the total tax for this item
            $orderitem_tax += $product_tax_rate * $item->orderitem_final_price;
            
			$item->orderitem_tax = $orderitem_tax;            
            // track the running total
            $tax_total += $item->orderitem_tax;
        }        
    	 $this->order_tax = $tax_total;        
    }
    
    
      function calculateShippingTotals()
    {
        $order_shipping     = 0.00;
        
        $items =& $this->getItems();
        if (!is_array($items) || !$this->shipping)
        {
            $this->order_shipping       = $order_shipping;
            return;
        }
     
        // set object properties
		$this->order_shipping       = $this->shipping->shipping_price + $this->shipping->shipping_extra;       
		$this->shipping_method_id   = $this->shipping->shipping_method_id;           
    }
    
    
    /**
     * Calculates the per_order coupon discount for the order
     * and the total post-tax/shipping discount
     * and sets order->order_discount
     * 
     * @return unknown_type
     */
    function calculateDiscountTotals()
    {
        $total = 0.00;
        
        //get global discount percentage
        $params = &JComponentHelper::getParams('com_j2store');
        $discount_percentage = trim($params->get('global_discount'));
        
        if (empty($discount_percentage))
        {
            $this->order_discount = $total;
            return;
        }
      
		//calculate the discount amount  
        $amount = ($discount_percentage/100) * ($this->order_subtotal + $this->order_tax);
        $total = $amount;
        
        // store the total amount of the discount  
        //set the total as equal to the order_subtotal + order_tax if its greater than the sum of the two
        $this->order_discount = $total > ($this->order_subtotal + $this->order_tax) ? $this->order_subtotal + $this->order_tax : $total;
        
    }
	
	  function getItems()
    {
        // TODO once all references use this getter, we can do fun things with this method, such as fire a plugin event
        JModel::addIncludePath( JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'models' );
        // if empty($items) && !empty($this->order_id), then this is an order from the db,  
        // so we grab all the orderitems from the db  
        if (empty($this->_items) && !empty($this->id))
        {
            // TODO Do this?  How will this impact Site::TiendaControllerCheckout->saveOrderItems()?
            //retrieve the order's items
            $model = JModel::getInstance( 'OrderItems', 'J2StoreModel' );
            $model->setState( 'filter_orderid', $this->order_id);
            $model->setState( 'order', 'tbl.orderitem_name' );
            $model->setState( 'direction', 'ASC' );
            $orderitems = $model->getList();
            foreach ($orderitems as $orderitem)
            {
                unset($table);
                $table = JTable::getInstance( 'OrderItems', 'Table' );
                $table->load( $orderitem->orderitem_id );
                $this->addItem( $table );
            }
        }
        
        $items =& $this->_items;        
        if (!is_array($items))
        {
            $items = array();
        }
        $this->_items = $items;
        return $this->_items;
    }
    
    
     function getInvoiceNumber( $refresh=false )
    {
        if (empty($this->_order_number) || $refresh)
        {
            $nullDate   = $this->_db->getNullDate();
            if (empty($this->created_date) || $this->created_date == $nullDate)
            {
                $date = JFactory::getDate();
                $this->created_date = $date->toMysql();
            }
            $order_date = JHTML::_('date', $this->created_date, '%Y%m%d');
            $order_time = JHTML::_('date', $this->created_date, '%H%M%S');
            $user_id = $this->user_id;
            $this->_order_number = $order_date.'-'.$order_time.'-'.$user_id;            
        }

        return $this->_order_number;
    }
    
      /**
     * Gets the order's shipping total object
     * 
     * @return object
     */
    function getShippingTotal( $refresh=false )
    {				    
    	return $this->_shipping_total;
    }
    
    function getDiscountTotal( $refresh=false )
    {
		
		$total = 0.00;
        
        //get global discount percentage
        $params = &JComponentHelper::getParams('com_j2store');
        $discount_percentage = trim($params->get('global_discount'));
        
        if (empty($discount_percentage))
        {
            $order_discount = $total;
            return;
        }
      
		//calculate the discount amount  
        $amount = ($discount_percentage/100) * ($this->order_subtotal + $this->order_tax);
        $total = $amount;
        
        // store the total amount of the discount  
        //set the total as equal to the order_subtotal + order_tax if its greater than the sum of the two
        $order_discount = $total > ($this->order_subtotal + $this->order_tax) ? $this->order_subtotal + $this->order_tax : $total;
        
        return $order_discount;        
    }

    
    function save()
	{
        if ($return = parent::save())
        {
            // create the order_number when the order is saved
            if (empty($this->order_id) && !empty($this->id))
            {
                $this->order_id = time();
                $this->store();
            }
            
            // TODO All of the protected vars information could be saved here instead, no?	
        }
        return $return;
	}
	
}
?>
