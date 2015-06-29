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

class EasyBlogMediaManagerJomSocialSource
{
	private $relative	= null;
	private $path 		= null;
	private $fileName	= null;
	private $baseURI 	= null;
	private $exists		= null;

	public function __construct()
	{
		$file 	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $file ) )
		{
			$this->exists	= false;
		}

		require_once( $file );
		$this->exists	= true;
	}

	/**
	 * Returns an array of folders / albums in a given folder since jomsocial only stores user images here.
	 *
	 * @access	public
	 * @param	string	$path	The path that contains the items.
	 * @param	int 	$depth	The depth level to search for child items.
	 */
	public function getItems()
	{
		if( !$this->exists )
		{
			return false;
		}

		$my			= JFactory::getUser();

		// @rule: Get user's albums
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

		
		$model		= CFactory::getModel( 'photos' );

		// Always retrieve the list of albums first
		$albums		= $model->getAlbums( $my->id );


		if( !$albums )
		{
			$obj	= new stdClass();

			$obj->title		= JText::_( 'COM_EASYBLOG_MM_MY_ALBUMS' );
			$obj->type		= 'folder';
			$obj->contents	= array();

			return $obj;
		}

		$data	= array();

		foreach( $albums as $album )
		{
			$container	= array();

			// Get a list of photos from this album
			$photos		= $model->getPhotos( $album->id );

			// Get the album object.
			$albumObj	= $this->getObject( $album , 'album' );

			$photosData	= array();

			foreach( $photos as $photo )
			{
				// Get the photo object.
				$photoObj		= $this->getObject( $photo , 'photo' );
				$photosData[]	= $photoObj;
			}

			$albumObj->contents	= $photosData;

			$data[]				= $albumObj;
		}

 		return $data;
	}

	/**
	 * Returns the information of a photo object.
	 *
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function getItem( $photoId )
	{
		if( !$this->exists )
		{
			return false;
		}

		// First we need to get the item id.
		JTable::addIncludePath( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'tables' );
		$photo	= JTable::getInstance( 'Photo' , 'CTable' );
		$photo->load( $photoId );

		$obj				= $this->getObject( $photo , 'photo' );

		$obj->variations	= $this->getVariations( $photo );
		return $obj;
	}

	public function getVariations( &$photo )
	{
		// In JomSocial, there's only 2 variations, the original and the thumbnail.
		$sizes		= array( 'original' , 'thumbnail' );

		// If remote storage is used, try to not include thumbnail.
		if( !$photo->storage != 'file' )
		{
			$sizes	= array( 'original' );
		}

		$variations	= array();

		foreach( $sizes as $size )
		{
			$variation	= new stdClass();

			$photoObj					= JTable::getInstance( 'Photo' , 'CTable' );
			$photoObj->load( $photo->id );

			$info						= @getimagesize( JPATH_ROOT . DS . str_ireplace( '/' , DS , $photo->$size ) );

			$variation->type 			= 'image';
			$variation->name			= JText::_( 'COM_EASYBLOG_MM_VARIATION_' . strtoupper( $size ) );
			$variation->title			= $photo->caption;

			// @task: Get the absolute URI to the item.
			$useSize					= $size == 'thumbnail' ? $size : '';
			$variation->url				= $photoObj->getImageURI( $useSize );

			// @task: Get the creation date of the item.
			$variation->creationDate 			= JFactory::getDate( $photo->created )->toMySQL();

			// @task: Set the thumbnail
			$variation->thumbnail			= new stdClass();
			$variation->thumbnail->url 	= $photoObj->getImageURI( 'thumbnail' );

			$variation->relativePath	= '';

			$variation->mime			= 'n/a';
			
			$variation->width			= false;
			$variation->height			= false;	

			// If this is a file type, we'll try to stat the image
			if( $photo->storage == 'file' )
			{
				$info						= @getimagesize( JPATH_ROOT . DS . str_ireplace( '/' , DS , $photo->$size ) );
				$variation->mime			= $info[ 'mime' ];
				
				$variation->width			= $info[0];
				$variation->height			= $info[1];
			}

			$variation->default			= false;
			
			if( $size == 'thumbnail' )
			{
				$variation->default			= true;
			}

			$variation->canDelete		= false;
			$variation->size			= $photo->filesize;
			$variations[]				= $variation;
		}

		return $variations;
	}

	private function getObject( $item , $type )
	{
		$obj 			= new stdClass();

		if( $type == 'album' )
		{
			$obj->type 		= 'folder';

			// @task: Get the media item's title.
			$obj->title		= $item->name;

			// @task: Get the media item's width.
			$obj->width 	= '';

			// @task: Get the media item's height.
			$obj->height 	= '';

			// @task: Get the mime type
			$obj->mime 		= '';

			// @task: Determine the filesize of this item (Bytes).
			$obj->filesize 	= '';

			// @task: Get the absolute URI to the item.
			$obj->uri 		= rtrim( JURI::root() , '/' ) . '/' . $item->path;

			// @task: Get the creation date of the item.
			$obj->creationDate 	= JFactory::getDate( $item->created )->toMySQL();

			// @task: Get the contents 
			// @todo
			$obj->path			= DS . $item->id;

			$obj->icon 			= new stdClass();
			$obj->icon->url		= rtrim( JURI::root() , '/' ) . '/components/com_easyblog/assets/images/gallery.png';
			
			$obj->contents		= '';

		}
		else if( $type == 'photo' )
		{

			$photoObj		= JTable::getInstance( 'Photo' , 'CTable' );
			$photoObj->load( $item->id );

			$obj->type 		= 'image';

			// @task: Get the media item's title.
			$obj->title		= $item->caption;

			$info			= @getimagesize( JPATH_ROOT . DS . str_ireplace( '/' , DS , $item->image ) );

			// @task: Get the media item's width.
			$obj->width 	= $info[ 0 ];

			// @task: Get the media item's height.
			$obj->height 	= $info[ 1 ];

			// @task: Get the mime type
			$obj->mime 		= $info[ 'mime' ];

			// @task: Determine the filesize of this item (Bytes).
			$obj->filesize 	= $item->filesize;

			// @task: Get the absolute URI to the item.
			$obj->url 			= $photoObj->getImageURI();

			// @task: Get the creation date of the item.
			$obj->creationDate 	= JFactory::getDate( $item->created )->toMySQL();

			// @task: Set the thumbnail
			$obj->thumbnail			= new stdClass();
			$obj->thumbnail->url 	= $photoObj->getImageURI( 'thumbnail' );

			// @task: Set the thumbnail
			$obj->icon				= new stdClass();
			$obj->icon->url			= $photoObj->getImageURI( 'thumbnail' );

			// @task: Get the contents 
			// @todo
			$obj->path		= DS . $item->id;

			// Set empty variation.
			$obj->variations	= '';
		}

		return $obj;
	}
}