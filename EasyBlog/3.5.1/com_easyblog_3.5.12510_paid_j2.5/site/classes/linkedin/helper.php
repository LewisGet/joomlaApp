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

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_easyblog' . DS . 'constants.php' );
require_once( EBLOG_CLASSES . DS . 'linkedin' . DS . 'consumer.php' );

class EasyBlogLinkedIn extends LinkedIn
{
	public function __construct( $key , $secret , $callback )
	{
		parent::__construct( array( 'appKey' => $key ,
									'appSecret'	=> $secret,
									'callbackUrl' => $callback 
							) 
		);
	}
	
	public function getRequestToken()
	{
		$request	= $this->retrieveTokenRequest();
		
		$obj		= new stdClass();
		$obj->token		= $request['linkedin']['oauth_token'];
		$obj->secret	= $request['linkedin']['oauth_token_secret'];
		
		return $obj;
	}
	
	public function getAuthorizationURL( $token )
	{
		return parent::_URL_AUTH . $token;
	}
	
	public function getVerifier()
	{
		$verifier	= JRequest::getVar( 'oauth_verifier' , '' );
		return $verifier;
	}
	
	public function getAccess( $token , $secret , $verifier )
	{
		$access		= parent::retrieveTokenAccess( $token , $secret , $verifier );

		if( isset( $access['linkedin']['oauth_problem'] ) )
		{
			return false;
		}

		$obj		= new stdClass();

		$obj->token		= $access['linkedin']['oauth_token'];
		$obj->secret	= $access['linkedin']['oauth_token_secret'];
		$obj->params	= '';
		
		//@todo: expiry
		
		return $obj;
	}
	
	/**
	 * Shares a new content on LinkedIn
	 **/	 	 	
	public function share( $blog , $message = '' , $oauth , $useSystem = false )
	{
		$message = $this->processMessage( $message , $blog );

		$pattern	= '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
		preg_match( $pattern , $blog->content , $matches );

		$image		= '';
		
		if( isset( $matches[1] ) )
		{
			$image	= $matches[1];

			if( JString::stristr( $matches[1], 'http://' ) === false && !empty( $image ) )
			{
				$image	= rtrim(JURI::root(), '/') . '/' . ltrim( $image, '/');
			}
		}

		$text		= strip_tags( $blog->content );

		// Linkedin now restricts the message and text size.
		$message	= JString::substr( $message , 0 , 700 );
		$text		= JString::substr( $text , 0 , 256 );

		$content	= array(
							'title'			=> $blog->title,
							'comment' 		=> $message,
							'submitted-url'	=> EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id , false , true ),
							'submitted-image-url'	=> $image,
							'description'			=> $text
							);

		$status		= parent::share( 'new' , $content , true , false );
		
		return $status['success'] == true;
	}
	
	public function setAccess( $access )
	{
		$access	= new JParameter( $access );
		return parent::setTokenAccess( array('oauth_token' => $access->get('token') , 'oauth_token_secret' => $access->get( 'secret') ) );
	}
	
	public function revokeApp()
	{
		$result	= parent::revoke();
		
		return $result['success'] == true;
	}

	/**
	 * Process message
	 **/
	function processMessage( $message , $blog)
	{
		$config		= EasyBlogHelper::getConfig();
		$message 	= empty( $message ) ? $config->get( 'main_linkedin_message' ) : $message;
		$search		= array();
		$replace	= array();
		
		//replace title
		if (preg_match_all("/.*?(\\{title\\})/is", $message, $matches))
		{
			$search[] = '{title}';
		    $replace[] = $blog->title;
		}
		
		//replace title
		if (preg_match_all("/.*?(\\{introtext\\})/is", $message, $matches))
		{
			$introtext = empty($blog->intro)? '' : strip_tags( $blog->intro );
			
			$search[] = '{introtext}';
		    $replace[] = $introtext;
		}
		
		//replace category
		if (preg_match_all("/.*?(\\{category\\})/is", $message, $matches))
		{
			$category 	= EasyBlogHelper::getTable( 'ECategory', 'Table' );
			$category->load($blog->category_id);
			
			$search[]	= '{category}';
		    $replace[]	= $category->title;
		}

		//replace link
		if (preg_match_all("/.*?(\\{link\\})/is", $message, $matches))
		{
			$link = EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $blog->id, false, true);
			$search[]	= '{link}';
			$replace[]	= $link;
		}
		
		$message = JString::str_ireplace($search, $replace, $message);
		
		return $message;
	}
}