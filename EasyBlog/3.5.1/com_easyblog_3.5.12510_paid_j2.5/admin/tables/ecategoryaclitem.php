<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'constants.php' );
require_once( EBLOG_HELPERS . DS . 'router.php' );

class TableECategoryAclItem extends JTable
{
	/*
	 * The id of the category acl item
	 * @var int
	 */
	var $id 			= null;
	var $action			= null;
	var $description	= null;
	var $published		= null;
	var $default		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_category_acl_item' , 'id' , $db );
	}

	function getAllRuleItems()
	{
		$db = JFactory::getDBO();

		$query  = 'select * from `#__easyblog_category_acl_item` order by id';
		$db->setQuery($query);

		return $db->loadObjectList();
	}

}
