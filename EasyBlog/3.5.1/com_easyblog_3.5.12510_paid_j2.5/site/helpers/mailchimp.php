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

jimport( 'joomla.filesystem.file' );

class EasyBlogMailchimpHelper
{
	var $key	= null;
	var $url	= 'api.mailchimp.com/1.3/';

	public function __construct()
	{
		$config 	= EasyBlogHelper::getConfig();
		$this->key 	= $config->get( 'subscription_mailchimp_key');


		if( $this->key )
		{
			$datacenter	= explode( '-' , $this->key );

			$this->url	= 'http://' . $datacenter[1] . '.' . $this->url;
		}
	}

	public function subscribe( $email , $firstName , $lastName = 'test' )
	{
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );
		$config = EasyBlogHelper::getConfig();

		if( !function_exists( 'curl_init' ) )
		{
			echo JText::_( 'COM_EASYBLOG_CURL_DOES_NOT_EXIST' );
		}

		if( !$config->get( 'subscription_mailchimp' ) )
		{
			return;
		}

		$listId	= $config->get( 'subscription_mailchimp_listid' );

		if( !$listId )
		{
			return;
		}

		$firstName 	= urlencode( $firstName );
		$lastName 	= urlencode( $lastName );

		$sendWelcome 	= $config->get( 'subscription_mailchimp_welcome' ) ? 'true' : 'false';

		$url	= $this->url . '?method=listSubscribe';
		$url	= $url . '&apikey=' . $this->key;
		$url	= $url . '&id=' . $listId;
		$url	= $url . '&output=json';
		$url	= $url . '&email_address=' . $email;
		$url	= $url . '&merge_vars[FNAME]=' . $firstName;
		$url	= $url . '&merge_vars[LNAME]=' . $lastName;
		$url	= $url . '&merge_vars[email_type]=html';
		$url	= $url . '&merge_vars[send_welcome]=' . $sendWelcome;

		$ch		= curl_init( $url );

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0 );
		$result = curl_exec($ch);
		curl_close($ch);

		return true;
	}
}
