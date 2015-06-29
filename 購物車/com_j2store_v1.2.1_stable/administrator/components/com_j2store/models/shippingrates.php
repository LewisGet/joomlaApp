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
require_once(JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'models'.DS.'_base.php');

class J2StoreModelShippingRates extends J2StoreModelBase
{
    protected function _buildQueryWhere(&$query)
    {
        $filter_id	= $this->getState('filter_id');
        $filter_shippingmethod  = $this->getState('filter_shippingmethod');
        $filter_weight = $this->getState('filter_weight');
       	
		if (strlen($filter_id))
        {
            $query->where('tbl.shipping_rate_id = '.(int) $filter_id);
       	}
        if (strlen($filter_shippingmethod))
        {
            $query->where('tbl.shipping_method_id = '.(int) $filter_shippingmethod);
        }
     
        if (strlen($filter_weight))
        {
        	$query->where("(
        		tbl.shipping_rate_weight_start <= '".$filter_weight."' 
        		AND (
                    tbl.shipping_rate_weight_end >= '".$filter_weight."'
                    OR
                    tbl.shipping_rate_weight_end = '0.000'
                    )
			)");
       	}
    	
    }
   
    protected function _buildQueryFields(&$query)
    {
        $field = array();
        //$field[] = " geozone.geozone_name ";
        
        $query->select( $this->getState( 'select', 'tbl.*' ) );     
        $query->select( $field );
    }
        	
	public function getList()
	{
		
		$list = parent::getList(); 
	
		// If no item in the list, return an array()
        if( empty( $list ) ){
        	return array();
        }
	
		foreach($list as $item)
		{
			$item->link_remove = 'index.php?option=com_j2store&view=shippingrates&task=delete&cid[]='.$item->shipping_rate_id;
		}
		
		return $list;
	}
	
	
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	
}
