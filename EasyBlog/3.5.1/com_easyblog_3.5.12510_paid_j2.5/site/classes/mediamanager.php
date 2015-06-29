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

require_once( EBLOG_CLASSES . DS . 'json.php' );

class EasyBlogMediaManager
{
	private $source 	= null;
	private $json 		= null;
	private $items 		= array();
	private $item		= null;

	/**
	 *
	 * @access	public
	 */
	public function __construct( $type = EBLOG_MEDIA_SOURCE_LOCAL )
	{
		$source		= dirname( __FILE__ ) . DS . 'mediamanager' . DS . $type . '.php';

		if( !JFile::exists( $source ) )
		{
			return false;
		}

		require_once( $source );

		$sourceClass	= 'EasyBlogMediaManager' . ucfirst( $type ) . 'Source';

		if( !class_exists( $sourceClass ) )
		{
			return false;
		}

		$this->source 	= new $sourceClass();
		$this->json 	= new Services_JSON();
	}


	// Assuming that the storage path is /home/admin/images
	//
	// absolutePath - /home/admin/images/subfolder/filename.png OR /home/admin/images/filename.png
	// relativePath - /subfolder/filename.png OR /filename.png
	// baseURI 		- http://site.com/images
	public function getItems( $absolutePath , $baseURI , $relativePath = '' , $variation = false , $flatList = false , $exclude = array( 'index.html' , '.svn', 'CVS', '.DS_Store', '__MACOSX') )
	{
		// @task: Ensure that the baseURI doesn't contain any trailing /
		$baseURI			= rtrim( $baseURI , '/' );

		$this->items 		= $this->source->getItems( $absolutePath , $baseURI , $relativePath , $variation , $flatList , $exclude );

		return $this;
	}

	/**
	 * Retrieves a single item object given the absolute path to that particular item.
	 */
	public function getItem( $absolutePath , $baseURI , $relativePath = '' , $variation = false, $place = '' )
	{
		// @task: Ensure that the baseURI doesn't contain any trailing /
		$baseURI			= rtrim( $baseURI , '/' );

		$this->items		= $this->source->getItem( $absolutePath , $baseURI , $relativePath , $variation, $place );
		return $this;
	}

	/**
	 * Creates a variation item based on the original image.
	 * This only works with image type currently.
	 *
	 * @access	public
	 * @param	string 	$absolutePath
	 * @param	string 	$variationName
	 * @param	int		$width
	 * @param	int		$height
	 */
	public function createVariation( $absolutePath , $absoluteURI , $variationName , $width , $height , $variationType = EBLOG_VARIATION_USER_TYPE )
	{
		return $this->source->createVariation( $absolutePath , $absoluteURI , $variationName , $width , $height , $variationType );
	}

	/**
	 * Creates a variation item based on the original image.
	 * This only works with image type currently.
	 *
	 * @access	public
	 * @param	string 	$absolutePath
	 * @param	string 	$variationName
	 */
	public function deleteVariation( $absolutePath , $variationName )
	{
		return $this->source->deleteVariation( $absolutePath , $variationName );
	}

	/**
	 * Renames an item.
	 *
	 * @access	public
	 * @param	string	$source			The absolute path to the source item.
	 * @param 	string	$destination	The absolute path to the destination.
	 * @return	mixed		True on success, error message (string) otherwise.
	 */
	public function rename( $source , $destination )
	{
		if( !method_exists( $this->source , 'rename' ) )
		{
			return JText::_( 'COM_EASYBLOG_RENAME_OPERATION_NOT_SUPPORTED' );
		}

		return $this->source->rename( $source , $destination );
	}

	/**
	 * Returns a list of items in JSON format.
	 */
	public function toJSON( $exit = false )
	{
		$data	= $this->json->encode( $this->items );

		if( $exit )
		{
			header('Content-type: text/x-json; UTF-8');
			echo $data;
			exit;
		}

		return $data;
	}

	/**
	 * Returns the array of items in php.
	 *
	 * @access	public
	 */
	public function toArray()
	{
		return $this->items;
	}

	/**
	 * Returns the array of items in php.
	 *
	 * @access	public
	 */
	public function toObject()
	{
		$data 	= (object) $this->toArray();
		return $data;
	}

	/**
	 * Responsible to handle file uploads
	 */
	public function upload( $storagePath , $storageURI , $file , $relativePath )
	{
		return $this->source->upload( $storagePath , $storageURI , $file , $relativePath );
	}

	/**
	 * Triggers the delete method from the source API
	 */
	public function delete( $file )
	{
		if( method_exists( $this->source , 'delete' ) )
		{
			return $this->source->delete( $file );
		}

		return false;
	}

	/**
	 * Determins if an item exists.
	 *
	 * @access	public
	 * @param	string	$path	The path to the item.
	 * @return	boolean			True on success, false otherwise.
	 */
	public function exists( $path )
	{
		if( !method_exists( $this->source , 'exists' ) )
		{
			return false;
		}

		return $this->source->exists( $path );
	}

	/**
	 * Retrieves the absolute path to a specific item given the relative path and storage type.
	 *
	 * @access	public
	 * @param	string	$relativePath	The relative path to the item.
	 * @param	string 	$storeType		Whether this is 'user' , 'article' or 'shared'
	 *
	 * @return	string					The absolute path to the item.
	 */
	public static function getAbsolutePath( $relativePath , $place )
	{
		$cfg		= EasyBlogHelper::getConfig();

		$place 		= explode( ':' , $place );
		$storeType	= $place[ 0 ];

		// @rule: Compatibility fixes! Since user = main_image_path , change 'user' to 'image'.
		if( $storeType == 'user' )
		{
			$storeType	= 'image';
		}

		if( $storeType == 'shared' && !$cfg->get( 'main_media_manager_place_shared_media' ) )
		{
			return false;
		}

		// @task: Get the root path first.
		$path		= JPATH_ROOT;

		// @task: Now we need to append the trailing path which is the storetype paths.
		$configuredPath		= str_ireplace( array( '/' , '\\' ) , DS , $cfg->get( 'main_' . $storeType . '_path' ) );

		// @task: Trim trailing and preceeding slashes if any.
		$configuredPath 	= trim( $configuredPath , DS );

		// @task: For now, we just ensure that the user id uses the logged in user's id.
		// In the future, we could use $place[1] to determine the id if the admin want's to impersonate someone.
		if( $storeType == 'image' )
		{
			$my 				= JFactory::getUser();
			$configuredPath		.= DS . $my->id;
		}

		// @task: Merge the paths.
		$path	.= DS . $configuredPath;

		// @task: Ensure that the relativePath is set to the proper directory separator.
		$relativePath	= str_ireplace( array( '/' , '\\' ) , DS , $relativePath );

		// @task: Let's join the relative path back to the full path.
		$path	= $path . DS . ltrim( $relativePath , DS );

		return $path;
	}

	/**
	 * Retrieves the absolute URI to a specific item given the relative path and storage type.
	 *
	 * @access	public
	 * @param	string	$relativePath	The relative path to the item.
	 * @param	string 	$storeType		Whether this is 'user' , 'article' or 'shared'
	 *
	 * @return	string					The absolute path to the item.
	 */
	public static function getAbsoluteURI( $relativePath , $place )
	{
		$cfg		= EasyBlogHelper::getConfig();

		$place 		= explode( ':' , $place );
		$storeType	= $place[ 0 ];

		// @rule: Compatibility fixes! Since user = main_image_path , change 'user' to 'image'.
		if( $storeType == 'user' )
		{
			$storeType	= 'image';
		}

		// @task: Get the root path first.
		$path		= rtrim( JURI::root() , '/' );

		// @task: Now we need to append the trailing path which is the storetype paths.
		$configuredPath		= str_ireplace( '\\' , '/' , $cfg->get( 'main_' . $storeType . '_path' ) );

		// @task: Trim trailing and preceeding slashes if any.
		$configuredPath 	= trim( $configuredPath , '/' );

		// @task: For now, we just ensure that the user id uses the logged in user's id.
		// In the future, we could use $place[1] to determine the id if the admin want's to impersonate someone.
		if( $storeType == 'image' )
		{
			$my 	= JFactory::getUser();
			$configuredPath		.= '/' . $my->id;
		}

		// @task: Merge the paths.
		$path	.= '/' . $configuredPath;

		// @task: Ensure that the relativePath is set to the proper directory separator.
		$relativePath	= str_ireplace( '\\' , '/' , $relativePath );

		// @task: Let's join the relative path back to the full path.
		$path	= $path . '/' . ltrim( $relativePath , '/' );

		return $path;
	}

	public function createThumbnail( $fileName , $source , $destination )
	{
		$obj 			= new stdClass();
		$obj->width		= EBLOG_MEDIA_ICON_WIDTH;
		$obj->height	= EBLOG_MEDIA_ICON_HEIGHT;
		$obj->resize 	= 'within';

		require_once( EBLOG_CLASSES . DS . 'image.php' );
		$imageObj		= new EasyBlogImage( $fileName , dirname( $destination ) , '' );

		$imageObj->initDefaultSizes();
	}
}
