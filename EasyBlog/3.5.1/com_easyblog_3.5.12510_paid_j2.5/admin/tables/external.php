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

class TableExternal extends JTable
{
	var $id 		= null;
	var $source		= null;
	var $post_id	= null;
	var $uid 		= null;

	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_external' , 'id' , $db );
	}
	
	function loadBySource( $postId , $key , $source )
	{
		$db		= JFactory::getDBO();
		
		$query	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $key ) . ' '
				. 'AND ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId ) . ' '
				. 'AND ' . $db->nameQuote( 'source' ) . '=' . $db->Quote( $source );
		
		$db->setQuery( $query );
		$obj	= $db->loadObject();
		
		return parent::bind( $obj );
	}
}