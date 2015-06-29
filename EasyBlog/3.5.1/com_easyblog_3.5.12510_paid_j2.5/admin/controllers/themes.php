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

jimport('joomla.application.component.controller');

class EasyBlogControllerThemes extends EasyBlogController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'apply' , 'save' );
	}

	/**
	 * Make the provided theme a default theme for EasyBlog
	 */
	public function makeDefault()
	{
		JRequest::checkToken( 'request' ) or die( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'theme' );

		$msg    	= '';

		$theme 	= JRequest::getWord( 'element' );


		if( !empty($theme) )
		{
			$config	= EasyBlogHelper::getConfig();

			$config->set( 'layout_theme' , $theme );

			$table	= EasyBlogHelper::getTable( 'Configs' , 'Table' );
			$table->load( 'config' );

			$table->params	= $config->toString( 'INI' );
			$table->store();

			// Clear the component's cache
			$cache = JFactory::getCache('com_easyblog');
			$cache->clean();

			$msg    = JText::sprintf( 'COM_EASYBLOG_THEME_SET_AS_DEFAULT' , $theme );
		}


		$this->setRedirect( 'index.php?option=com_easyblog&view=themes' , $msg , 'success' );
	}

	public function save()
	{
		JRequest::checkToken() or die( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'theme' );

		$element 	= JRequest::getVar( 'element' );
		$params 	= JRequest::getVar( 'params' );
		$obj 		= new JParameter( '' );

		foreach( $params as $key => $value )
		{
			$obj->set( $key , $value );
		}

		$this->updateBlogImage( $element );

		// Store this value in the configs table.
		$table 	= EasyBlogHelper::getTable( 'Configs' );
		$table->load( $element );
		$table->set( 'name'		, $element );
		$table->set( 'params'	, $obj->toString( 'INI' ) );
		$table->store( $element );

		$url 		= $this->getTask() == 'apply' ? 'index.php?option=com_easyblog&view=theme&element=' . $element : 'index.php?option=com_easyblog&view=themes';

		$this->setRedirect( $url , JText::_( 'COM_EASYBLOG_THEME_SAVED_SUCCESSFULLY' ) , 'success' );
	}

	public function updateBlogImage( $element )
	{
		// Let's update the blog image.
		$blogImageFile	=	EBLOG_THEMES . DS . DS . $element . DS . 'image.ini';

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $blogImageFile ) )
		{
			return false;
		}

		$contents 	= JFile::read( $blogImageFile );

		require_once( EBLOG_CLASSES . DS . 'json.php' );

		$json 		= new Services_JSON();
		$types		= $json->decode( $contents );
		$modified	= false;

		foreach( $types as $type )
		{
			if( $type->name == 'frontpage' || $type->name == 'entry' )
			{
				$width		= JRequest::getVar( 'blogimage_' . $type->name . '_width' );
				$height		= JRequest::getVar( 'blogimage_' . $type->name . '_height' );
				$resize 	= JRequest::getVar( 'blogimage_' . $type->name . '_resize' );
				$visible	= JRequest::getBool( 'blogimage_' . $type->name );

				if( !empty( $width ) && !empty( $height ) && !empty($resize) )
				{
					$type->width	= $width;
					$type->height	= $height;
					$type->resize 	= $resize;
					$type->visible	= $visible;

					$modified	= true;
				}
			}
		}

		if( $modified )
		{
			// Now let's save this
			$contents	= $json->encode( $types );

			$state 		= JFile::write( $blogImageFile , $contents );
		}
	}

	public function getAjaxTemplate()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'theme' );

		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT . DS . 'administrator' );
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		$files	= JRequest::getVar( 'names' , '' );

		if( empty( $files ) )
		{
			return false;
		}

		// Ensure the integrity of each items submitted to be an array.
		if( !is_array( $files ) )
		{
			$files	= array( $files );
		}

		$result		= array();

		foreach( $files as $file )
		{
			$dashboard = explode( '/' , $file );

			if( $dashboard[0]=="dashboard" )
			{
				$template 	= new CodeThemes( true );
				$out		= $template->fetch( $dashboard[1] . '.ejs' );
			}
			elseif ( $dashboard[0]=="media" )
			{
				$template 	= new CodeThemes( true );
				$out		= $template->fetch( "media." . $dashboard[1] . '.ejs' );
			}
			else
			{
				$template 	= new CodeThemes();
				$out		= $template->fetch( $file . '.ejs' );
			}

			$obj			= new stdClass();
			$obj->name		= $file;
			$obj->content	= $out;

			$result[]		= $obj;
		}


		header('Content-type: text/x-json; UTF-8');
		$json	 		= new Services_JSON();
		echo $json->encode( $result );
		exit;
	}
}
