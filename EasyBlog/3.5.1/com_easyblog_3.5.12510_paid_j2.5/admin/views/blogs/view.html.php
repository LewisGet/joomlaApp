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

require( EBLOG_ADMIN_ROOT . DS . 'views.php');

class EasyBlogViewBlogs extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('core.manage.blog' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		JHTML::_('behavior.tooltip');

		$filter_state		= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_state', 		'filter_state', 	'*', 'word' );
		$search				= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.search', 			'search', 			'', 'string' );
		$filter_category	= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_category', 	'filter_category', 	'*', 'int' );
		$filterLanguage		= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_language', 	'filter_language', 	'', '' );
		$search				= trim(JString::strtolower( $search ) );
		$order				= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection		= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_order_Dir',	'filter_order_Dir',	'', 'word' );
		$source				= JRequest::getVar( 'filter_source' , '-1' );
		$filteredBlogger	= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_blogger' , 'filter_blogger' , '' , 'int' );
		
		//Get data from the model
		$blogs			= $this->get( 'Blogs' );
		$pagination		= $this->get( 'Pagination' );

		$catFilter		= $this->getFilterCategory( $filter_category );

		$browse			= JRequest::getInt( 'browse' , 0 );
		$browsefunction	= JRequest::getVar('browsefunction', 'insertBlog');

		


		// @task: Get the centralized oauth consumers
		$consumers				= array();
		$sites					= array( 'twitter' , 'facebook' , 'linkedin' );
		$centralizedConfigured  = false;

		foreach( $sites as $site )
		{
			$consumer	= EasyBlogHelper::getTable( 'OAuth' );
			$consumer->loadSystemByType( $site );

			if( !empty( $consumer->id ) )
				$centralizedConfigured  = true;

			$consumers[]	= $consumer;
		}

		$this->assignRef( 'consumers'	, $consumers );
		$this->assignRef( 'centralizedConfigured'	, $centralizedConfigured );
		$this->assignRef( 'source' 		, $source );
		$this->assign( 'filterLanguage'	, $filterLanguage );
		$this->assign( 'filteredBlogger' , $filteredBlogger );
		$this->assign( 'browse' , $browse );
		$this->assign( 'browseFunction' , $browsefunction );
		$this->assignRef( 'blogs' 		, $blogs );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assign( 'state'			, $this->getFilterState($filter_state));
		$this->assign( 'category'		, $catFilter );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	public function getLanguageTitle( $code )
	{
		$db 	= JFactory::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'title' ) . ' FROM '
				. $db->nameQuote( '#__languages' ) . ' WHERE '
				. $db->nameQuote( 'lang_code' ) . '=' . $db->Quote( $code );
		$db->setQuery( $query );

		$title 	= $db->loadResult();

		return $title;
	}

	public function getFilterBlogger( $filter_type = '*' )
	{
		$bloggersModel	= $this->getModel( 'Blogger' );
		$bloggers		= $bloggersModel->getBloggers( 'alphabet' , null , 'showbloggerwithpost' );
		$filter[]		= JHTML::_('select.option', '', '- '. JText::_( 'Select Blogger' ) .' -' );
		foreach( $bloggers as $blogger )
		{
			$filter[] = JHTML::_('select.option', $blogger->id, $blogger->name );
		}

		return JHTML::_('select.genericlist', $filter, 'filter_blogger', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_type );
	}

	function getCategories($filter_type = '*')
	{
		$filter[]	= JHTML::_('select.option', '', '- '. JText::_( 'Select Category' ) .' -' );

		$model		= $this->getModel( 'Categories' );
		$categories	= $model->getAllCategories();

		foreach($categories as $cat)
		{
			$filter[] = JHTML::_('select.option', $cat->id, $cat->title );
		}

		return JHTML::_('select.genericlist', $filter, 'filter_category', 'class="inputbox" size="1"', 'value', 'text', $filter_type );
	}

	function getFilterCategory($filter_type = '*')
	{
		$filter[]	= JHTML::_('select.option', '', '- '. JText::_( 'COM_EASYBLOG_SELECT_CATEGORY' ) .' -' );

		$model		= $this->getModel( 'Categories' );
		$categories	= $model->getAllCategories();

		foreach($categories as $cat)
		{
			$filter[] = JHTML::_('select.option', $cat->id, $cat->title );
		}

		return JHTML::_('select.genericlist', $filter, 'filter_category', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_type );
	}

	function getFilterState ($filter_state='*')
	{
		$state[] = JHTML::_('select.option',  '', '- '. JText::_( 'Select State' ) .' -' );
		$state[] = JHTML::_('select.option',  'P', JText::_( 'COM_EASYBLOG_PUBLISHED' ) );
		$state[] = JHTML::_('select.option',  'U', JText::_( 'COM_EASYBLOG_UNPUBLISHED' ) );
		$state[] = JHTML::_('select.option',  'S', JText::_( 'COM_EASYBLOG_SCHEDULED' ) );
		$state[] = JHTML::_('select.option',  'T', JText::_( 'COM_EASYBLOG_TRASHED' ) );
		return JHTML::_('select.genericlist',   $state, 'filter_state', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_state );
	}

	function getCategoryName( $id )
	{
		$category	= EasyBlogHelper::getTable( 'ECategory' , 'Table');
		$category->load( $id );
		return JText::_( $category->title );
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_BLOGS_ALL_BLOG_ENTRIES_TITLE' ), 'blogs' );

		JToolBarHelper::back( JText::_( 'COM_EASYBLOG_HOME' ) , 'index.php?option=com_easyblog' );
		JToolBarHelper::divider();
		JToolBarHelper::custom('addNew','new.png','new_f2.png', JText::_( 'COM_EASYBLOG_ADD_BUTTON' ) , false);
		JToolBarHelper::divider();

		if( JRequest::getVar('filter_state' ,'' ) != 'T' )
		{
			JToolBarHelper::custom( 'feature' , 'eblog-feature' , '' , JText::_( 'COM_EASYBLOG_FEATURE_TOOLBAR' ) );
			JToolBarHelper::custom( 'unfeature' , 'eblog-unfeature' , '' , JText::_( 'COM_EASYBLOG_UNFEATURE_TOOLBAR' ) );
			JToolbarHelper::publishList();
			JToolbarHelper::unpublishList();
			JToolBarHelper::divider();
		}

		JToolBarHelper::custom( 'showMove' , 'eblog-move' , '' , JText::_( 'COM_EASYBLOG_MOVE' ) );
		JToolBarHelper::custom( 'copy' , 'eblog-copy' , '' , JText::_( 'COM_EASYBLOG_COPY' ) );
		JToolBarHelper::divider();


		// If this is on the trash view, we need to show empty trash icon
		if( JRequest::getVar('filter_state') == 'T' )
		{
			JToolbarHelper::publishList( 'publish' , JText::_( 'COM_EASYBLOG_RESTORE' ) );
			JToolbarHelper::deleteList();
		}
		else
		{
			JToolbarHelper::trash( 'trash' );
		}
	}
}
