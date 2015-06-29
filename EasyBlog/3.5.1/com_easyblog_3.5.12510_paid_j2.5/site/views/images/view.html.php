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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_media' . DS . 'helpers' . DS . 'media.php' );

class EasyBlogViewImages extends EasyBlogView
{
	public function loadScript($scripts)
	{
		$document	= JFactory::getDocument();
		$version = str_ireplace('.', '', EasyBlogHelper::getLocalVersion());

		foreach($scripts as $script)
		{
			$document->addScript(JURI::root() . $script . '?' . $version);
		}
	}

	public function flickrLogin()
	{
		$doneLogin	= JRequest::getVar( 'doneLogin' );

		if ($doneLogin) {

			echo '<script type="text/javascript">';
			echo 'window.opener.location.reload();';
			echo 'window.close();';
			echo '</script>';
			exit;
		}

		$theme = new CodeThemes( true );

		// @task: Set the redirect so that after granting access, our oauth library knows where to redirect the user to.
		// $redirect = base64_encode( rtrim( JURI::root() , '/' ) . '/index.php?option=com_easyblog&view=images&layout=flickrLogin&doneLogin&tmpl=component' );
		$redirect = base64_encode("javascript: alert('1');");
		$theme->set( 'redirect' , $redirect );

		echo $theme->fetch( 'media.flickrlogin.php' );
		return;
	}

	/**
	 * Display images from Flickr
	 */
	public function flickr()
	{
		$my			= JFactory::getUser();
		$config		= EasyBlogHelper::getConfig();
		$profile	= EasyBlogHelper::getTable( 'Profile' );
		$profile->load( $my->id );

		// @rule: Test if the user is already associated with Flickr
		$oauth		= EasyBlogHelper::getTable( 'Oauth' );
		$associated	= $oauth->loadByUser( $my->id , EBLOG_OAUTH_FLICKR );

		if( !$associated ) return;

		$media	= new EasyBlogMediaManager( EBLOG_MEDIA_SOURCE_FLICKR );
		$media->getItems( '' , '' );

		$media->toJSON( true );

		// $theme 	= new CodeThemes( true );
		// // @task: Set the redirect for revoking app.
		// $redirect	= base64_encode( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=images&layout=flickr' , false ) );
		// $theme->set( 'redirect'	, $redirect );

		// $theme->set( 'photos'	, $photos );

		// echo $theme->fetch( 'media.flickr.php' );
	}

	/**
	 * Display albums and photos from JomSocial
	 */
	public function jomsocial()
	{
		$config		= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$profile	= EasyBlogHelper::getTable( 'Profile' );
		$profile->load( $my->id );

		// @rule: Test if the user is really logged in or not.
		if( $my->id <= 0 )
		{
			return;
		}

		$media		= new EasyBlogMediaManager( EBLOG_MEDIA_SOURCE_JOMSOCIAL );
		$albums		= $media->getItems( 3 , '' );

		$albums->toJSON( true );

 		$theme		= new CodeThemes( true );
 		$theme->set( 'albums'	, $albums );

 		echo $theme->fetch( 'media.jomsocial.php' );
	}

	/**
	 * Displays the files and folders that are in the media manager.
	 */
	public function display($tpl = null)
	{
		$config     = EasyBlogHelper::getConfig();
		$document	= JFactory::getDocument();
		$my         = JFactory::getUser();
		$user		= JFactory::getUser( JRequest::getInt('blogger_id', $my->id) );
		$app		= JFactory::getApplication();

		if( $my->id <= 0 )
		{
			echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			exit;
		}

		$sharedFolder		= JRequest::getVar( 'shared' );

		$main_image_path	= $config->get('main_image_path');
		$main_image_path 	= rtrim($main_image_path, '/');

		$imagePath			= str_replace('/', DS, $main_image_path . DS . $user->id);
		$imagePathBase 		= $main_image_path . '/' . $user->id;

		$uploadPath 		= rtrim( JPATH_ROOT, '/' ) . DS . str_ireplace( '/' , DS , $main_image_path . DS . $user->id );
		$uploadPathBase 	= '/' . $main_image_path . '/' . $user->id;

		if( $sharedFolder && $system->config->get( 'main_media_manager_place_shared_media' ) )
		{
			// Retrieve the path to the shared folder.
			$main_image_path	= $config->get( 'main_sharedpath' );

			// @rule: The image path should be changed to the shared path now.
			$imagePath 			= str_ireplace( '/' , DS , $config->get( 'main_sharedpath' ) );
			$imagePathBase		= $main_image_path . '/';

			$uploadPath 		= rtrim( JPATH_ROOT, '/' ) . DS . str_ireplace( '/' , DS , $main_image_path );
			$uploadPathBase		= '/' . $main_image_path . '/';
		}

		// If the path doesn't exist, we need to create it.
  		if( !JFolder::exists( $uploadPath ) )
  		{
  		    JFolder::create( $uploadPath );
	        $source 		= JPATH_ROOT . DS . 'components' . DS . 'com_easyblog' . DS . 'index.html';
			$destination	= $uploadPath . DS .'index.html';
        	JFile::copy( $source , $destination );
  		}

  		$folder				= JRequest::getVar( 'folder' );

  		$uploadPath 		.= DS . str_ireplace( '/' , DS , $folder );

		if( !empty( $folder ) )
		{
			$imagePathBase	.= '/' . $folder;
		}

		// @task: Retrieve the list of the files.
		$images 	= $this->getFiles( $uploadPath , JURI::root() . $imagePathBase , $folder );

		// Set the path so that javascript can process the parts.
		$path		= '';

		if( $folder )
		{
			$path	= '/' . $folder;
		}

		$debug		= ( $config->get( 'debug_javascript') || JRequest::getVar( 'ebjsdebug' ) == 1 ) ? 'true' : 'false';

		$theme		= new CodeThemes( true );
		$theme->set( 'debug'		, $debug );
		$theme->set( 'path' 		, $path );
		$theme->set( 'baseURL'		,	JURI::root() . $imagePathBase );
		$theme->set( 'session'		, JFactory::getSession() );
		$theme->set( 'images'		, $images );
		$theme->set( 'blogger_id'	, $user->id );


		$whoami = JRequest::getVar( 'whoami' );

		if ($whoami) {

			echo $theme->fetch( 'media.' . $whoami . '.php' );

		} else {

			echo $theme->fetch( 'media.php' );
		}
	}

	public function getFiles( $folder , $baseURL , $subfolder = '' )
	{
		static $list;

		if (is_array($list))
		{
			return $list;
		}

		// @rule: Retrieve folders
		$folders	= JFolder::folders( $folder , '.' , false , true );
		$foldersData= array();

		if( $folders )
		{
			foreach( $folders as $curFolder )
			{
			    // fixed for path in window enviroment in joomla 1.7.
				$curFolder   = str_ireplace( '/' , DS , $curFolder );
				$curFolder	= str_ireplace( $folder . DS , '' , $curFolder );


				$foldersData[] = EasyBlogHelper::getHelper( 'ImageData' )->getFolderObject( $folder , $curFolder , $baseURL , $subfolder );
			}
		}

		// Retrieve files
		$files		= JFolder::files( $folder , '.' , false , true );
		$data		= array();

		array_multisort(
    		array_map( 'filectime', $files ),
    		SORT_NUMERIC,
    		SORT_DESC,
    		$files
		);

		if( $files )
		{
			foreach( $files as $file )
			{
			    // fixed for path in window enviroment in joomla 1.7.
				$file   = str_ireplace( '/' , DS , $file );
				$file	= str_ireplace( $folder . DS , '' , $file );

				if( is_file( $folder . DS . $file ) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html' && stristr( $file , EBLOG_MEDIA_THUMBNAIL_PREFIX ) === false )
				{
					$data[] = EasyBlogHelper::getHelper( 'ImageData' )->getObject( $folder , $file , $baseURL , $subfolder );
				}
			}
		}
		krsort( $data );

		$data	= array_merge( $foldersData , $data );

		return $data;
	}


}
