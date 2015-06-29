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

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pane' );
require( EBLOG_ADMIN_ROOT . DS . 'views.php');

class EasyBlogViewAutoposting extends EasyBlogAdminView
{

	var $rules = array();

	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('core.manage.autoposting' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();

		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal' , 'a.modal' );

		$config		= EasyBlogHelper::getConfig();
		$layout		= $this->getLayout();

		if( method_exists( $this , $layout ) )
		{
			$this->$layout( $tpl );

			return;
		}

		$isFacebookAssociated	= EasyBlogHelper::getHelper( 'OAuth' )->isAssociated( 'facebook' );
		$isTwitterAssociated	= EasyBlogHelper::getHelper( 'OAuth' )->isAssociated( 'twitter' );
		$isLinkedinAssociated	= EasyBlogHelper::getHelper( 'OAuth' )->isAssociated( 'linkedin' );

		$this->assignRef( 'config'	, $config );
		$this->assignRef( 'isFacebookAssociated', $isFacebookAssociated );
		$this->assignRef( 'isTwitterAssociated', $isTwitterAssociated );
		$this->assignRef( 'isLinkedinAssociated', $isLinkedinAssociated );

		parent::display($tpl);
	}

	public function form( $tpl = null )
	{
		JHTML::_('behavior.tooltip');

		$type	= JRequest::getVar( 'type' );
		$config	= EasyBlogHelper::getConfig();

		$isAssociated = EasyBlogHelper::getHelper( 'OAuth' )->isAssociated( $type );

		$oauth		= EasyBlogHelper::getTable( 'Oauth' );
		$oauth->loadSystemByType( $type );

		$this->assignRef( 'oauth'			, $oauth );
		$this->assignRef( 'isAssociated' 	, $isAssociated );
		$this->assignRef( 'config'	, $config );
		$this->assignRef( 'type'	, $type );

		parent::display($tpl);
	}

	public function facebook( $tpl = null )
	{
		$step	= JRequest::getVar( 'step' );
		$config	= EasyBlogHelper::getConfig();

		$isAssociated	=  EasyBlogHelper::getHelper( 'OAuth' )->isAssociated( __FUNCTION__ );

		$oauth		= EasyBlogHelper::getTable( 'Oauth' );
		$oauth->loadSystemByType( 'facebook' );

		$expire 	= '';

		if( $oauth->id )
		{
			$expires	= $oauth->getAccessTokenValue( 'expires' );
			$created 	= strtotime( $oauth->created );

			$expire 	= JFactory::getDate( $expires + $created )->toFormat( '%A, %d %B %Y' );
		}

		$this->assignRef( 'expire'		 , $expire );
		$this->assignRef( 'isAssociated' , $isAssociated );
		$this->assignRef( 'config'	, $config );
		$this->assignRef( 'step'	, $step );

		parent::display($tpl);
	}

	public function twitter( $tpl = null )
	{
		$step	= JRequest::getVar( 'step' );
		$config	= EasyBlogHelper::getConfig();


		$isAssociated	=  EasyBlogHelper::getHelper( 'OAuth' )->isAssociated( __FUNCTION__ );

		$this->assignRef( 'isAssociated' , $isAssociated );
		$this->assignRef( 'config'	, $config );
		$this->assignRef( 'step'	, $step );

		parent::display($tpl);
	}

	public function linkedin( $tpl = null )
	{
		$step	= JRequest::getVar( 'step' );
		$config	= EasyBlogHelper::getConfig();


		$isAssociated	=  EasyBlogHelper::getHelper( 'OAuth' )->isAssociated( __FUNCTION__ );

		$this->assignRef( 'isAssociated' , $isAssociated );
		$this->assignRef( 'config'	, $config );
		$this->assignRef( 'step'	, $step );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_AUTOPOSTING' ), 'autoposting' );

		if( $this->getLayout() == 'default' )
		{
			JToolBarHelper::back( JText::_( 'COM_EASYBLOG_HOME' ) , 'index.php?option=com_easyblog' );
		}
		else
		{
			JToolBarHelper::back( JText::_( 'COM_EASYBLOG_BACK' ) , 'index.php?option=com_easyblog&view=autoposting' );
		}

		if( $this->getLayout() == 'form' )
		{
			JToolBarHelper::divider();
			JToolBarHelper::save( 'saveForm' );
			JToolBarHelper::apply( 'applyForm' );
		}
	}
}
