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

require_once( EBLOG_HELPERS . DS . 'imagedata.php' );

class EasyBlogViewImages extends EasyBlogView
{

	private function output( $response )
	{
		include_once( EBLOG_CLASSES . DS . 'json.php' );
		$json	= new Services_JSON();
		echo $json->encode( $response );
		exit;
	}

	private function getMessageObj( $code = '' , $message = '', $item = false )
	{
		$obj			= new stdClass();
		$obj->code		= $code;
		$obj->message	= $message;
		
		if( $item )
		{
			$obj->item	= $item;
		}
		
		return $obj;
	}
	
	public function createFolder( $currentPath , $folderName )
	{
		$ajax		= new Ejax();
		$my			= JFactory::getUser();
		$config     = EasyBlogHelper::getConfig();
		$acl		= EasyBlogACLHelper::getRuleSet();

		// @rule: Only allowed users are allowed to upload images.
		if( $my->id == 0 || empty( $acl->rules->upload_image ) )
		{
			echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			exit;
		}

		// @rule: Sanitize and clean up current path and folder names
		$currentPath	= str_ireplace( array('..','../') , '' , $currentPath );
		$folderName		= str_ireplace( array('..','../') , '' , $folderName );

		$imagePath	= JPATH_ROOT . DS . rtrim( $config->get('main_image_path') , '/' ) . DS . $my->id;
		$baseURI	= JURI::root() . rtrim( $config->get( 'main_image_path' ) , '/' ) . '/' . $my->id;

		// @rule: Do not continue if folder doesn't even exists.
		if( !JFolder::exists( $imagePath ) )
		{
			$ajax->error( JText::_( 'COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_ERROR' ) );
			$ajax->send();
		}

		// @rule: Get absolute path to the current path.
		$absolutePath		= $imagePath;
		
		// @rule: sanitize folder name.
		if( !empty( $currentPath ) )
		{
			$absolutePath	= $imagePath . DS . $currentPath;
		}

		$folderName	= str_ireplace( ' ' , '_' , $folderName );

		if( JFolder::exists( $absolutePath . DS . $folderName ) )
		{
			$ajax->error( JText::_( 'COM_EASYBLOG_FOLDER_EXISTS' ) );
			$ajax->send();
		}
		
		JFolder::create( $absolutePath . DS . $folderName );
		$source 		= JPATH_ROOT . DS . 'components' . DS . 'com_easyblog' . DS . 'index.html';
		$destination	= $absolutePath . DS . $folderName . DS .'index.html';
        JFile::copy( $source , $destination );


		$data	= EasyBlogImageDataHelper::getFolderObject( $absolutePath , $folderName , $baseURI , ltrim( $currentPath ,'/' ) );

		$ajax->callback( '' , $data );

		return $ajax->send();
	}
	
	public function deleteItem( $fileName , $fileType , $relativePath )
	{
		$ajax		= new Ejax();
		$config		= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();

		// @rule: Only allowed users are allowed to upload images.
		if( $my->id == 0 || empty( $acl->rules->upload_image ) )
		{
			$ajax->error( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) );
			return $ajax->send();
		}

		// Get the main image path
		$imagePath	= str_ireplace( array( "/" , "\\" ) , DS , rtrim( $config->get('main_image_path') , '/' ) );
		$path		= JPATH_ROOT . DS .$imagePath . DS . $my->id;
		
		// Fix all paths since we use forward slashes on the frontend part.
		$relativePath	= str_ireplace( '/' , DS , $relativePath );
		$item			= $path . DS . $relativePath;
		
		if( is_file( $item ) )
		{
			// Test to see if main file exists
			if( !JFile::exists( $item ) )
			{
				$ajax->error( JText::_( 'COM_EASYBLOG_FILE_DOES_NOT_EXIST' ) );
				return $ajax->send();
			}
			
			if( $fileType == 'image' )
			{
				// Test to see if thumbnail exists
				$thumb		= EBLOG_MEDIA_THUMBNAIL_PREFIX . $fileName;
				$thumbItem	= str_ireplace( $fileName , $thumb , $item );
				
				if( JFile::exists( $thumbItem ) )
				{
					JFile::delete( $thumbItem );
				}
			}

			// Delete the file
			JFile::delete( $item );
		}
		
		if( is_dir( $item ) )
		{
			if( !JFolder::exists( $item ) )
			{
				$ajax->error( JText::_( 'COM_EASYBLOG_FOLDER_DOES_NOT_EXIST' ) );
				return $ajax->send();
			}
			
			JFolder::delete( $item );
		}
		
		$ajax->callback( '' );
		
		return $ajax->send();
	}
}
