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

jimport( 'joomla.application.component.view');

class EasyBlogViewUsers extends JView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('core.manage.user' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document		= JFactory::getDocument();
		$user			= JFactory::getUser();
		$mainframe		= JFactory::getApplication();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easyblog.users.filter_state', 		'filter_state', 	'*', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easyblog.users.search', 			'search', 			'', 'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easyblog.users.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easyblog.users.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		//Get data from the model
		$users			= $this->get( 'Users' );
		$pagination		= $this->get( 'Pagination' );

		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			if(count($users) > 0)
			{
				for($i = 0; $i < count($users); $i++)
				{
					$row    			= $users[$i];
					$row->usergroups 	= $this->getGroupTitle( $row->id );
				}
			}
		}

		$browse			= JRequest::getInt( 'browse' , 0 );
		$browsefunction = JRequest::getVar('browsefunction', 'insertMember');
		$browseUID		= JRequest::getVar( 'uid' , '' );

		$this->assign( 'browseUID' , $browseUID );
		$this->assign( 'browse' , $browse );
		$this->assign( 'browsefunction' , $browsefunction );
		$this->assignRef( 'users' 		, $users );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	function getGroupTitle( $user_id )
	{
		$db = JFactory::getDbo();
		$sql = "SELECT title FROM ".$db->nameQuote('#__usergroups')." ug left join ".
				$db->nameQuote('#__user_usergroup_map')." map on (ug.id = map.group_id)".
				" WHERE map.user_id=".$user_id;

		$db->setQuery($sql);
		$result = $db->loadResultArray();
		return nl2br( implode("\n", $result) );
	}

	function getPostCount( $id )
	{
		$db	= JFactory::getDBO();

		$query	= 'SELECT COUNT(1) FROM #__easyblog_post '
				. 'WHERE `created_by`=' . $db->Quote( $id );
		$db->setQuery( $query );

		return $db->loadResult();
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_BLOGGERS_TITLE' ), 'users' );

		JToolBarHelper::back( JText::_( 'COM_EASYBLOG_HOME' ) , 'index.php?option=com_easyblog' );
		JToolBarHelper::divider();
		JToolbarHelper::addNew();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
	}
}
