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

jimport('joomla.application.component.model');

/**
 *
 * @package		Joomla
 * @subpackage	J2Store
 * @since 1.5
 */
class J2StoreModelOrders extends JModel
{
	/**
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$mainframe = &JFactory::getApplication();
		$option = 'com_j2store';

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 *
	 * @access public
	 * @return integer
	 */
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

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = ' SELECT a.*, u.name AS buyer, u.email as bemail, oi.user_email, oi.user_id as oiuserid, oi.billing_first_name'
			. ' FROM #__j2store_orders AS a '
			. ' LEFT JOIN #__users AS u ON u.id = a.user_id '
			. ' LEFT JOIN #__j2store_orderinfo AS oi ON oi.orderpayment_id = a.id '
			. $where
			. $orderby
		;

		return $query;
	}

	function _buildContentOrderBy()
	{
				$mainframe = &JFactory::getApplication();
		$option = 'com_j2store';

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'a.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		
		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , a.created_date';
		
		return $orderby;
	}

	function _buildContentWhere()
	{
		$mainframe = &JFactory::getApplication();
		$option = 'com_j2store';
		
		$db					=& JFactory::getDBO();
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'a.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$filter_orderstate	= $mainframe->getUserStateFromRequest( $option.'filter_orderstate',	'filter_orderstate',	'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'search',			'search',			'',				'string' );
		if (strpos($search, '"') !== false) {
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);

		$where = array();

		if ($search) {
			$where[] = 'LOWER(a.order_id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ).
			           'OR LOWER(a.transaction_status) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ).
			           'OR LOWER(a.orderpayment_type) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		if($filter_orderstate) {
			if($filter_orderstate == 'Confirmed') {
				$where[] = 'a.order_state = '.$db->Quote($db->getEscaped( $filter_orderstate, true ),false);
			} else if($filter_orderstate == 'Pending') {
				$where[] = 'a.order_state = '.$db->Quote($db->getEscaped( $filter_orderstate, true ),false);
			} else if($filter_orderstate == 'Failed') {
				$where[] = 'a.order_state = '.$db->Quote($db->getEscaped( $filter_orderstate, true ),false);
			}	
		}

		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	
	function delete($cid = array())
	{
		$result = false;

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			
			//lets first  delete the order attributes if any
			$this->_deleteOrderAttributes($cid);
			
			//let us first delete the order items for this order
			$result = $this->_deleteOrderItems($cid);
			
			$cids = implode( ',', $cid );
			
			if($result) {
				$query = 'DELETE FROM #__j2store_orders'
					. ' WHERE id IN ( '.$cids.' )';
				$this->_db->setQuery( $query );
				if(!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			} else {
				$this->setError('Could not delete order items');
				return false;
			}
		}

		return true;
	}
	
	
	function _deleteOrderItems($cid) {
		
		foreach($cid as $id) {
			
			$order_id = $this->_getOrderID($id);
			
			if($order_id) {				
				$query = 'DELETE FROM #__j2store_orderitems'
					. ' WHERE order_id = '.$this->_db->Quote($this->_db->getEscaped( $order_id, true ),false);
				$this->_db->setQuery( $query );
				if(!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			} else {
				return false;	
			}			
				
		}	//end of foreach
		
		return true;
			
	}
	
	
	function _deleteOrderAttributes($cid) {
		
		foreach($cid as $id) {
			
			$order_item_ids = $this->_getOrderItemIDs($id);		
			if(count($order_item_ids)) {
				
				foreach($order_item_ids as $orderitem_id) { 				
				echo $query = 'DELETE FROM #__j2store_orderitemattributes'
					. ' WHERE orderitem_id = '. (int) $orderitem_id;
				$this->_db->setQuery( $query );
				$this->_db->query();
				}				
			}			
				
		}	//end of foreach
		
		return true;
		
	}
	
		
	function _getOrderID($id) {
			
			$db = & JFactory::getDBO();
			$query = "SELECT order_id FROM #__j2store_orders WHERE id={$id}";
			$db->setQuery($query);
			return $db->loadResult();	
			
	}
	
	function _getOrderItemIDs($id) {
		
		//first get the order_id
		$order_id = $this->_getOrderID($id);
		
		//get the order item ids
		$db = & JFactory::getDBO();
		$query = "SELECT orderitem_id FROM #__j2store_orderitems WHERE order_id=".$db->Quote($order_id);
		$db->setQuery($query);
		return $db->loadResultArray();
	}
		
}
