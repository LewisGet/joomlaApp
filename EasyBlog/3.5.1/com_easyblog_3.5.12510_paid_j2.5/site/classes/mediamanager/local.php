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

class EasyBlogMediaManagerLocalSource
{
	private $types	= array(
							'jpg'	=> 'image',
							'png'	=> 'image',
							'gif'	=> 'image',
							'bmp'	=> 'image',
							'jpeg'	=> 'image',
							'mp4'	=> 'video',
							'swf'	=> 'video',
							'flv'	=> 'video',
							'mov'	=> 'video',
							'f4v'	=> 'video',
							'3gp'	=> 'video',
							'aac'	=> 'video',
							'm4a'	=> 'video',
							'm4v'	=> 'video',
							'webm'	=> 'video',
							'ogv'	=> 'video',
							'mp3'	=> 'audio',
							'ogg'	=> 'audio'
						);

	private $relative	= null;
	private $path 		= null;
	private $fileName	= null;
	private $baseURI 	= null;
	private $includeVariation	= null;
	private $place		= null;

	/**
	 * Returns an array of items that are available in a given path.
	 *
	 * @access	public
	 * @param	string	$path	The path that contains the items.
	 * @param	int 	$depth	The depth level to search for child items.
	 */
	public function getItems( $absolutePath , $baseURI , $relativePath = '' , $variation = false , $flatList = false , $exclude = array( 'index.html' , '.svn', 'CVS', '.DS_Store', '__MACOSX')  )
	{
		// @task: Assign the absolute path
		$this->path 	= $absolutePath;

		// @task: Assign the base URI.
		$this->baseURI	= $baseURI;

		// @task: Assign the relative path
		$this->relative = $relativePath;

		// @task: Include variations?
		$this->includeVariation	= $variation;

		// @task: Assign the file name
		$this->fileName	= basename( $absolutePath );

		$folders	= JFolder::folders( rtrim( $absolutePath , DS ) , '.' , $flatList , true );
		$files		= JFolder::files( rtrim( $absolutePath , DS )  , '.' , $flatList , true , $exclude );

		$items		= $this->mix( $folders , $files );
		$result 	= array();

		foreach( $items as $item )
		{
			// @task: We need to ignore all types of variations.
			if( stristr( $item , EBLOG_SYSTEM_VARIATION_PREFIX ) === false && stristr( $item , EBLOG_USER_VARIATION_PREFIX ) === false && stristr( $item , EBLOG_BLOG_IMAGE_PREFIX ) === false )
			{
				// @task: Legacy support for old "thumbnail_"
				if( stristr( $item , EBLOG_MEDIA_THUMBNAIL_PREFIX ) === false )
				{
					$result[]	= $this->getObject( $item , $flatList );
				}
			}
		}
		return $result;
	}

	/**
	 * Retrieves a single item object given the absolute path to that particular item.
	 */
	public function getItem( $absolutePath , $baseURI , $relativePath = '' , $variation = false, $place = '' , $isUpload = false )
	{
		// @task: Assign the absolute path
		$this->path 	= $absolutePath;

		// @task: Assign the base URI.
		$this->baseURI	= $baseURI;

		// @task: Assign the relative path
		$this->relative = $relativePath;

		// @task: Include variations?
		$this->includeVariation	= $variation;

		$this->place            = $place;

		return $this->getObject( $absolutePath , false , $isUpload );
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
		$mediaItem	= $this->getTypeObject( $absolutePath );

		return $mediaItem->createVariation( $absoluteURI , $variationName , $width , $height , $variationType );
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
		$mediaItem	= $this->getTypeObject( $absolutePath );

		return $mediaItem->deleteVariation( $variationName );
	}

	/**
	 * Merge both folder and files and sort them based on the creation time.
	 */
	private function mix()
	{
		$args	= func_get_args();

		if( count( $args ) <= 0 )
		{
			return array();
		}

		$items 	= $args[0];

		if( count( $args ) > 1 )
		{
			for( $i = 1; $i < count( $args ); $i++ )
			{
				$items	= array_merge( $items , $args[ $i ] );
			}
		}

		// @task: Sort the result.
		array_multisort(
    		array_map( 'filectime', $items ),
    		SORT_NUMERIC,
    		SORT_DESC,
    		$items
		);

		return $items;
	}

	private function getType( $path )
	{
		// Unknown extensions will use the types/file.php
		$classType 	= 'file';

		if( is_dir( $path ) )
		{
			$classType	= 'folder';
		}

		if( $classType != 'folder' )
		{
			$extension	= JString::strtolower( JFile::getExt( $path ) );

			if( isset( $this->types[ $extension ] ) )
			{
				$classType	= $this->types[ $extension ];
			}
		}

		return $classType;
	}

	private function getTypeObject( $path , $flatList = false , $isUpload = false )
	{
		$classType	= $this->getType( $path );

		// @task: Let's try to see if this library exists in our known list.
		$lib 	= dirname( __FILE__ ) . DS . 'types' . DS . strtolower( $classType ) . '.php';

		if( !JFile::exists( $lib ) )
		{
			return false;
		}

		require_once( $lib );

		$className	= 'EasyBlogMediaManager' . ucfirst( $classType );
		$mediaItem	= new $className( $path , $this->baseURI , $this->relative , $this->includeVariation , $flatList , $this->path , $isUpload );

		return $mediaItem;
	}

	private function getObject( $item , $flatList = false , $isUpload = false )
	{
		$mediaItem		= $this->getTypeObject( $item , $flatList , $isUpload );
		$obj 			= new stdClass();

		// @task: The media type.
		$obj->type 		= $mediaItem->getType();

		// @task: Get the media item's title.
		if( $obj->type == 'folder' &&  !empty( $this->place ))
		{
			$place 		= explode( ':' , $this->place );
			$storeType	= $place[ 0 ];

			if( $storeType == 'user' )
			{
				$obj->title		= JText::_( 'COM_EASYBLOG_MM_MY_MEDIA' );
			}
			else
			{
				$obj->title		= JText::_( 'COM_EASYBLOG_MM_SHARED_MEDIA' );
			}

		}
		else
		{
			$obj->title		= $mediaItem->getTitle();
		}

		// @task: Get the mime type
		$obj->mime 		= $mediaItem->getMime();

		// @task: Determine the filesize of this item (Bytes).
		$obj->filesize 		= $mediaItem->getSize();

		// @task: Get the absolute URI to the item.
		$obj->url 			= $mediaItem->getURI();

		// @task: Get the creation date of the item.
		$obj->creationDate 	= $mediaItem->getCreationDate();

		// @task: Get the contents
		// @todo
		$obj->path			= $mediaItem->getRelativePath();

		// @task: Allow the media item to inject it's own properties.
		$mediaItem->inject( $obj );

		return $obj;
	}

	/**
	 * Determins if an item exists.
	 *
	 * @access	public
	 * @param	string	$path	The path to the item.
	 * @return	boolean			True on success, false otherwise.
	 */
	public static function exists( $path )
	{
		// @rule: Test with JFolder first.
		if( @JFolder::exists( $path ) )
		{
			return true;
		}

		if( @JFile::exists( $path ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Delete's an item from the local storage.
	 *
	 * @access	public
	 * @param	string	$file	The path to the item.
	 */
	public function delete( $absolutePath )
	{
		$mediaItem		= $this->getTypeObject( $absolutePath );

		return $mediaItem->delete( $absolutePath );
	}

	public function getUniqueName( $storagePath , $fileName )
	{
		jimport( 'joomla.filesystem.file' );

		$i 	= 1;

		$itemPath 		= JPath::clean( $storagePath . DS . $fileName );

		// @task: Now, we need to ensure that this file doesn't exist on the system.
		if( JFile::exists( $itemPath ) )
		{

			while( JFile::exists( $itemPath ) )
			{
				// get file extension here:
				$ext         	= EasyImageHelper::getFileExtension($fileName);
				$ext         	= '.' . $ext;
				$tmpFileName 	= str_replace( $ext , '', $fileName);
				$tmp			= $tmpFileName . '_' . JFactory::getDate()->toFormat( "%Y%m%d-%H%M%S" ) . '_' . $i . $ext;

				// Reset the itempath.
				$itemPath	= JPath::clean( $storagePath . DS . $tmp );
				$i++;
			}

			// Get the new file name for this item.
			$fileName		= $tmp;
		}

		return $fileName;
	}

	/**
	 * Responsible to handle file uploads
	 *
	 */
	public function upload( $storagePath , $storageURI , $file , $relativePath )
	{
		// Cleanup any trailing slashes here.
		$storageURI			= rtrim( $storageURI , '/' );

		// Import necessary library.
		jimport( 'joomla.filesystem.file' );

		// Ensure that the file name is safe.
		$file[ 'name' ]		= JFile::makeSafe( $file[ 'name' ] );

		// Ensure that the file name does not contain UTF-8 data.
		$file[ 'name' ]		= trim( $file[ 'name' ] );
		$fileName			= $file[ 'name' ];

		//
		if(strpos( $fileName , '.' ) === false )
		{
			$fileName	= JFactory::getDate()->toFormat( "%Y%m%d-%H%M%S" ) . '.' . $fileName;
		}
		else if( strpos( $fileName , '.' ) == 0 )
		{
			$fileName	= JFactory::getDate()->toFormat( "%Y%m%d-%H%M%S" ) . $fileName;
		}

		// We do not want to allow spaces in the name.
		$fileName 		= str_ireplace(' ', '-', $fileName);

		// Try to see if there's a need to generate a unique file name.
		$fileName		= self::getUniqueName( $storagePath , $fileName );

		// Get the full path to this temporary item that would be uploaded.
		$itemPath	= JPath::clean( $storagePath . DS . $fileName );

		// @TODO: For now this is hardcoded to determine the type of the item. It is either an image or a file. That's it.
		$lib 			= dirname( __FILE__ ) . DS . 'types' . DS . 'item.php';
		$classType		= 'Item';

		if( self::isImage( $file[ 'tmp_name' ] ) )
		{
			$lib 		= dirname( __FILE__ ) . DS . 'types' . DS . 'image.php';
			$classType	= 'Image';
		}

		require_once( $lib );

		$className	= 'EasyBlogMediaManager' . ucfirst( $classType );

		// Let's pass this to the respective media items to process.
		// PHP 5.2 doesn't allow calling dynamic class variables for some reason.
		$obj 		= new $className( '' , '' );
		$result 	= $obj->upload( $storagePath , $fileName , $file );

		if( $result !== true )
		{
			return $result;
		}

		$obj 			= $this->getItem( $itemPath , $storageURI , $relativePath , true , '' , true );

		return $obj;
	}

	/**
	 * Returns the file type
	 */
	private function isImage( $path )
	{
		if( @getimagesize( $path ) === false )
		{
			return false;
		}

		return true;
	}
}
