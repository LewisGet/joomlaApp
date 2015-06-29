<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * Content Component Article Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class EasyBlogModelFeatured extends JModel
{

	var $_data = null;
	/**
	 * Record total
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
		
		$mainframe	= JFactory::getApplication();
		
		//$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');
		
		$limit		= EasyBlogHelper::getHelper( 'Pagination' )->getLimit();
	    $limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');
	    
		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);		

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);		
	}
	
	/**
	 * Method to get a pagination object for the categories
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
			$this->_pagination	= EasyBlogHelper::getPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}
	
	/**
	 * Method to get a pagination object for the categories
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
	

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		
		$db			= $this->getDBO();

		$query	 = 'SELECT a.*, b.`title` AS `category` FROM `#__easyblog_post` AS a';
		$query	.= ' LEFT JOIN `#__easyblog_category` AS b';
		$query	.= ' 	ON a.category_id = b.id';
		$query	.= ' INNER JOIN `#__easyblog_featured` AS c';
		$query	.= ' 	ON a.`id` = c.`content_id` AND c.`type` = ' . $db->Quote('post');
		$query  .= $where;
		$query  .= $orderby;

		return $query;
	}
	
	function _buildQueryWhere()
	{
		$mainframe	= JFactory::getApplication();
		$my 		= JFactory::getUser();
		$db			= $this->getDBO();


		$where = array();
		
		$where[] = ' a.`published` = 1';
		
		if($my->id == 0)
		    $where[]  = ' a.`private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	function _buildQueryOrderBy()
	{
		$orderby 	= ' ORDER BY a.`created` DESC';
		return $orderby;
	}
	
	function getFeaturedBlog()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}


    
}
