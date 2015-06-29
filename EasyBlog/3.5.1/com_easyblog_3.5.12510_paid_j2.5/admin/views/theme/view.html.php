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
require( EBLOG_ADMIN_ROOT . DS . 'views.php');

class EasyBlogViewTheme extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('core.manage.theme' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		JHTML::_( 'behavior.tooltip' );

		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config 	= EasyBlogHelper::getConfig();

		$element 	= JRequest::getWord( 'element' );
		$theme 		= EasyBlogHelper::getThemeObject( $element );

		$blogImageFile	= JPATH_ROOT . DS . 'components' . DS . 'com_easyblog' . DS . 'themes' . DS . $element . DS . 'image.ini';
		$blogImage 		= false;

		jimport( 'joomla.filesystem.file' );

		if( JFile::exists( $blogImageFile ) )
		{
			$contents		= JFile::read( $blogImageFile );

			require_once( JPATH_ROOT . DS . 'components' . DS.  'com_easyblog' . DS . 'classes' . DS . 'json.php' );

			$json 	= new Services_JSON();
			$types 	= $json->decode( $contents );

			foreach( $types as $type )
			{
				if( $type->name == 'frontpage' || $type->name == 'entry' )
				{
					$blogImage[ $type->name ]	= $type;
				}
			}
		}
		$this->assign( 'param'		, $this->getParams( $theme->name ) );
		$this->assign( 'blogImage' , $blogImage );
		$this->assign( 'theme' , $theme );
		$this->assign( 'config', $config );

		parent::display($tpl);
	}

	public function getParams( $theme )
	{
		static $param	= false;

		if( !$param )
		{
			$ini 		= EBLOG_THEMES . DS . $theme . DS . 'config.ini';
			$manifest	= EBLOG_THEMES . DS . $theme . DS . 'config.xml';
			$contents	= JFile::read( $ini );

			$param		= new JParameter( $contents , $manifest );

			$themeConfig	= EasyBlogHelper::getTable( 'Configs' );
			$themeConfig->load( $theme );

			// @rule: Overwrite with the settings from the database.
			if( !empty( $themeConfig->params ) )
			{
				$param->bind( $themeConfig->params );
			}
		}

		return $param;
	}

	public function renderParams( $theme , $group = '_default' )
	{
		$param 		= $this->getParams( $theme );

		$settings	= $param->getParams( 'params' , '_default' );


		$output 	= '';
		//var_dump( $settings );
		foreach( $settings as $setting )
		{

			$output		.= '<tr>';
			$output 	.= '<td class="key"><label>' . $setting[3] . '</label></td>';
			$output		.= '<td><div class="has-tip">';
			$output 	.= '	<div class="tip"><i></i>' . JText::_( $setting[ 2 ] ) . '</div>';

			// Cheap hack, to detect if this is a 'select list'.
			if( stristr( $setting[ 1 ] , '<select') !== false )
			{

				$output 	.= $setting[1];
			}
			else
			{
				$output 	.= '	' . $this->renderCheckbox( 'params[' . $setting[ 5 ] . ']' , $setting[ 4 ] );
			}
			$output 	.= '</div></td>';
			$output 	.= '</tr>';
		}

		return $output;
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_THEMES_TITLE' ), 'themes' );

		JToolBarHelper::back( JText::_( 'COM_EASYBLOG_BACK' ) , 'index.php?option=com_easyblog&view=themes' );
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'makedefault' , 'eblog-feature' , '' , JText::_( 'COM_EASYBLOG_SET_DEFAULT' ) , false );
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'enableall', 'easyblog-enableall', '', JText::_( 'COM_EASYBLOG_ENABLE_ALL' ), false );
		JToolBarHelper::custom( 'disableall', 'easyblog-disableall', '', JText::_( 'COM_EASYBLOG_DISABLE_ALL' ), false );
		JToolBarHelper::divider();
		JToolBarHelper::apply();
		JToolBarHelper::save();
	}
}
