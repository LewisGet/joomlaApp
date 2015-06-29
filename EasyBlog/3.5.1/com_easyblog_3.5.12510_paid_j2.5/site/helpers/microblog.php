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

class EasyBlogMicroBlogHelper
{
	function process()
	{
		$this->processMailbox();
		echo "\n";
		$this->processTwitter();
	}

	public function processMailbox()
	{
		/*
		 * Check enabled
		 */
		$config		= EasyBlogHelper::getConfig();
		$debug		= JRequest::getBool( 'debug', false );

		if (!$config->get( 'main_remotepublishing_mailbox' ))
		{
			return;
		}

		/*
		 * Check Prerequisites setting
		 */
		$userid 	= 0;

		if( $config->get( 'main_remotepublishing_mailbox_userid' ) == 0 && !$config->get( 'main_remotepublishing_mailbox_syncuser' ) )
		{
			echo 'Mailbox: Unspecified default user id.' . "<br />\n";
			return false;
		}

		/*
		 * Check time interval
		 */

		$interval	= (int) $config->get( 'main_remotepublishing_mailbox_run_interval' );
		$nextrun	= (int) $config->get( 'main_remotepublishing_mailbox_next_run' );
		$nextrun	= JFactory::getDate($nextrun)->toUnix();
		$timenow	= JFactory::getDate()->toUnix();

		if ($nextrun !== 0 && $timenow < $nextrun)
		{
			if (!$debug)
			{
				echo 'time now: ' . JFactory::getDate( $timenow )->toMySQL() . "<br />\n";
				echo 'next email run: ' . JFactory::getDate( $nextrun )->toMySQL() . "<br />\n";
				return;
			}
		}

		$txOffset	= EasyBlogDateHelper::getOffSet();
		$newnextrun	= JFactory::getDate('+ ' . $interval . ' minutes', $txOffset)->toUnix();

		// use $configTable to avoid variable name conflict
		$configTable		= EasyBlogHelper::getTable('configs');
		$configTable->load('config');
		$parameters = new JParameter($configTable->params);

		$parameters->set( 'main_remotepublishing_mailbox_next_run' , $newnextrun );
		$configTable->params = $parameters->toString('ini');

		$configTable->store();

		/*
		 * Connect to mailbox
		 */
		require_once(JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'classes'.DS.'mailbox.php');
		$mailbox		= new EasyblogMailbox;
		if (!$mailbox->connect())
		{
			$mailbox->disconnect();
			echo 'Mailbox: Could not connect to mailbox.';
			return false;
		}

		/*
		 * Get data from mailbox
		 */
		$total_mails	= $mailbox->getMessageCount();

		if ($total_mails < 1)
		{
			// No mails in mailbox
			$mailbox->disconnect();
			echo 'Mailbox: No emails found.';
			return false;
		}

		// Let's get the correct mails
		$prefix			= $config->get( 'main_remotepublishing_mailbox_prefix' );

		$search_criteria	= 'UNSEEN';

		if( !empty( $prefix ) )
		{
			$search_criteria	.= ' SUBJECT "'.$prefix.'"';
		}

		$sequence_list	= $mailbox->searchMessages( $search_criteria );

		if( $sequence_list===false )
		{
			// Email with matching subject not found
			$mailbox->disconnect();
			echo 'Mailbox: No matching mails found. ' . $search_criteria;
			echo ($debug) ? ' criteria: '.$search_criteria.' ' : '';
			return false;
		}

		/*
		 * Found the mails according to prefix,
		 * Let's process each of them
		 */
		$total	= 0;
		$enable_attachment	= $config->get( 'main_remotepublishing_mailbox_image_attachment' );
		$format				= $config->get( 'main_remotepublishing_mailbox_format' );
		$limit			 	= $config->get( 'main_remotepublishing_mailbox_fetch_limit' );

		// there's not limit function for imap, so we work around with the array
		// get the oldest message first
		sort($sequence_list);
		$sequence_list	= array_slice($sequence_list, 0, $limit);

		foreach ($sequence_list as $sequence)
		{
			// first, extract from the header
			$msg_info	= $mailbox->getMessageInfo($sequence);

			if ($msg_info === false)
			{
				echo 'Mailbox: Could not get message header.';
				echo ($debug) ? ' sequence:'.$sequence.' ' : '';
				continue;
			}

			$uid		= $msg_info->message_id;
			$date		= $msg_info->MailDate;
			$udate		= $msg_info->udate;
			$size		= $msg_info->Size;
			$subject	= $msg_info->subject;
			$from       = '';
			if( isset( $msg_info->from ) )
			{
				$senderInfo	= $msg_info->from[0];
				if( !empty( $senderInfo->mailbox ) && ! empty($senderInfo->host) )
					$from       = $senderInfo->mailbox . '@' . $senderInfo->host;
			}

			if( empty( $from ) )
			{
				$from		= $msg_info->fromemail;
			}

			// @rule: Try to map the sender's email to a user email on the site.
			if( $config->get( 'main_remotepublishing_mailbox_syncuser' ) )
			{
				$db		= JFactory::getDBO();
				$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__users' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'email' ) . '=' . $db->Quote( $from );
				$db->setQuery( $query );
				$userid 	= $db->loadResult();
			}
			else
			{
				// sync user email is not require. use the default selected user.
				$userid		= $config->get( 'main_remotepublishing_mailbox_userid' );
			}

			if( $userid == 0 )
			{
				echo 'Mailbox: Unable to detect the user based on the email ' . $from . "<br />\n";
				echo ($debug) ? ' sequence:'.$sequence.' ' : '';
				continue;
			}

			$date		= JFactory::getDate($date);
			$date		= $date->toMySQL();

			$subject	= str_ireplace($prefix, '', $subject);
			$filter		= JFilterInput::getInstance();
			$subject	= $filter->clean($subject, 'string');

			// @task: If subject is empty, we need to append this with a temporary string. Otherwise user can't edit it from the back end.
			if( empty( $subject ) )
			{
				$subject	= JText::_( 'COM_EASYBLOG_MICROBLOG_EMPTY_SUBJECT' );
			}

			// filter email according to the whitelist
			$filter		= JFilterInput::getInstance();
			$whitelist	= $config->get( 'main_remotepublishing_mailbox_from_whitelist' );
			$whitelist	= $filter->clean($whitelist, 'string');
			$whitelist	= trim($whitelist);

			if (!empty($whitelist))
			{
				// Ok. I bluffed we only accept comma seperated values. *wink*
				$pattern	= '([\w\.\-]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-z0-9\-]{2,4}))';

				preg_match_all( $pattern, $whitelist, $matches );
				$emails		= $matches[0];

				if (!in_array($from, $emails))
				{
					echo 'Mailbox: Message sender is block: #'.$sequence.' '.$subject;
					continue;
				}
			}


			// this is the magic
			$message	= new EasyblogMailboxMessage($mailbox->stream, $sequence);
			$message->getMessage();

			$html		= $message->getHTML();
			$plain		= $message->getPlain();
			$plain		= nl2br($plain);
			$body		= ($format=='html') ? $html : $plain;
			$body		= $body ? $body : $plain;

			$safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
			// JFilterInput doesn't strip css tags
			$body	= preg_replace("'<style[^>]*>.*?</style>'si", '', $body);
			$body	= $safeHtmlFilter->clean($body, 'html');
			$body	= trim($body);

			$attachments	= array();

			if ($enable_attachment)
			{
				$attachments	= $message->getAttachment();

				// process attached images
				if (!empty($attachments))
				{
					$config				= EasyBlogHelper::getConfig();
					$main_image_path	= $config->get('main_image_path');
					$main_image_path	= rtrim($main_image_path, '/');

					$rel_upload_path	= $main_image_path . '/' . $userid;

					$userUploadPath		= JPATH_ROOT . DS . $main_image_path . DS . $userid;
					$userUploadPath		= JPath::clean($userUploadPath);

					$dir				= $userUploadPath . DS;
					$tmp_dir			= JPATH_ROOT . DS . 'tmp' . DS;

					$uri				= JURI::base().$main_image_path.'/'.$userid.'/';

					if(! JFolder::exists($dir))
					{
						JFolder::create($dir);
					}

					foreach ($attachments as $attachment)
					{

						// clean up file name
						if(strpos($attachment['name'], '/') !== FALSE)
						{
							$attachment['name'] = substr($attachment['name'], strrpos($attachment['name'],'/')+1 );
						}
						elseif(strpos($attachment['name'], '\\' !== FALSE))
						{
							$attachment['name'] = substr($attachment['name'], strrpos($attachment['name'],'\\')+1 );
						}

						// @task: Store the file into a temporary location first.
						$attachment['tmp_name']	= $tmp_dir . $attachment['name'];
						JFile::write( $attachment['tmp_name'], $attachment['data']);


						require_once( EBLOG_CLASSES . DS . 'mediamanager.php' );

						// @task: Ensure that images goes through the same resizing format when uploading via media manager.
						$media 				= new EasyBlogMediaManager();
						$result 			= $media->upload( $dir , $uri , $attachment , '/' );

						// get the image file name and path
						if( is_object($result) && property_exists($result, 'title') )
						{
							$atmTitle = $result->title;
							$atmURL = $result->url;
						}
						else
						{
							$atmTitle = $attachment['name'];
							$atmURL	= $uri.$attachment['name'];
						}

						// @task: Once the attachment is processed, delete the temporary file.
						JFile::delete( $attachment['tmp_name'] );

						// now we need to replace the img tag in the email which the source is an attachment id :(
						$attachId   = $attachment['id'];
						if(! empty($attachId) )
						{
							$attachId   = str_replace('<', '', $attachId);
							$attachId   = str_replace('>', '', $attachId);

							$imgPattern  = array('/<div><img[^>]*src="[A-Za-z0-9:^>]*' . $attachId . '"[^>]*\/><\/div>/si',
												'/<img[^>]*src="[A-Za-z0-9:^>]*' . $attachId . '"[^>]*\/>/si');

							$imgReplace = array('','');
							$body		= preg_replace($imgPattern, $imgReplace, $body);
						}

						// insert image into blog post
						$body .= '<p><a class="easyblog-thumb-preview" href="'.$atmURL.'" title="'.$atmTitle.'"><img width="'.$config->get('main_thumbnail_width').'" title="'.$atmTitle.'." alt="" src="'.$atmURL.'" /></a></p>';
					}
				}
			}

			if ($format	== 'plain')
			{
				$body	= nl2br($body);
			}

			// tidy up the content so that the content do not contain incomplete html tag.
			$body   = EasyBlogHelper::getHelper('string')->tidyHTMLContent( $body );

			$type	= $config->get( 'main_remotepublishing_mailbox_type' );

			// insert $body, $subject, $from, $date
			$blog	= EasyBlogHelper::getTable( 'Blog' , 'Table' );

			// @task: Store the blog post
			$blog->set( 'title' 	, $subject );
			$blog->set( 'permalink' , EasyBlogHelper::getPermalink($blog->title) );
			$blog->set( 'source'	, 'email' );
			$blog->set( 'created_by', $userid );
			$blog->set( 'created'	, $date );
			$blog->set( 'modified'	, $date );
			$blog->set( 'publish_up', $date );
			$blog->set( $type		, $body );
			$blog->set( 'category_id', $config->get( 'main_remotepublishing_mailbox_categoryid' ) );
			$blog->set( 'published' , $config->get( 'main_remotepublishing_mailbox_publish' ) );
			$blog->set( 'frontpage'	, $config->get( 'main_remotepublishing_mailbox_frontpage' ) );
			$blog->set( 'issitewide', true );

			// @task: Set the blog's privacy here.
			$blog->set( 'private'	, $config->get( 'main_remotepublishing_mailbox_privacy' ) );

			// Store the blog post
			if (!$blog->store())
			{
				echo 'Mailbox: Message store failed. > ' . $blog->subject;
				continue;
			}


			// @rule: Autoposting to social network sites.
			if( $blog->published == POST_ID_PUBLISHED )
			{
				$blog->autopost( array( EBLOG_OAUTH_LINKEDIN , EBLOG_OAUTH_FACEBOOK , EBLOG_OAUTH_TWITTER ) , array( EBLOG_OAUTH_LINKEDIN , EBLOG_OAUTH_FACEBOOK , EBLOG_OAUTH_TWITTER ) );

				$blog->notify( false );
			}

			$total++;

			if( $mailbox->service == 'pop3' )
			{
				$mailbox->deleteMessage( $sequence );
			}

			if( $mailbox->service == 'imap' )
			{
				$mailbox->setMessageFlag($sequence, '\Seen');
			}
		}


		/*
		 * Disconnect from mailbox
		 */
		$mailbox->disconnect();

		/*
		 * Generate report
		 */
		echo JText::sprintf( '%1s blog posts fetched from mailbox: ' . $config->get( 'main_remotepublishing_mailbox_remotesystemname' ) . '.' , $total );
	}

	public function processTwitter()
	{
		// @rule: Find all oauth accounts
		$db		= JFactory::getDBO();
		$config	= EasyBlogHelper::getConfig();

		$key	= $config->get( 'integrations_twitter_api_key' );
		$secret	= $config->get( 'integrations_twitter_secret_key' );

		$query	= 'SELECT * FROM #__easyblog_oauth where `type`=' . $db->Quote( 'twitter' );
		$db->setQuery( $query );

		$accounts	= $db->loadObjectList();

		$hashes			= $config->get( 'integrations_twitter_microblog_hashes' );

		// If hashes are empty, do not try to run anything since we wouldn't be able to find anything.
		if( empty( $hashes ) )
		{
			return false;
		}

		$hashes			= explode( ',' , $hashes );
		$totalHashes	= count( $hashes );
		$search			= '';
		$categoryId		= $config->get( 'integrations_twitter_microblog_category' );
		$published		= $config->get( 'integrations_twitter_microblog_publish' );
		$frontpage		= $config->get( 'integrations_twitter_microblog_frontpage' );

		// Build the hash queries
		for( $i =0 ; $i < $totalHashes; $i++ )
		{
			$search	.= $hashes[ $i ];

			if( next( $hashes ) !== false )
			{
				$search	.= ' OR ';
			}
		}

		$total		= 0;

		if( $accounts )
		{
			foreach( $accounts as $account )
			{
				$query		= 'SELECT `id_str` FROM ' . $db->nameQuote( '#__easyblog_twitter_microblog' ) . ' '
							. 'WHERE ' . $db->nameQuote( 'oauth_id' ) . '=' . $db->Quote( $account->id ) . ' '
							. 'ORDER BY `created` DESC';

				$db->setQuery( $query );
				$result		= $db->loadObject();

				$jparam		= new JParameter( $account->params );
				$screen		= $jparam->get( 'screen_name' );

				// If we can't get the screen name, do not try to process it.
				if( !$screen )
				{
					continue;
				}

				// @rule: Retrieve the consumer object for this oauth client.
				$consumer	= EasyBlogHelper::getHelper( 'Oauth' )->getConsumer( 'twitter' , $key , $secret , '' );
				$consumer->setAccess( $accounts[0]->access_token );

				$params		= array( 'q' => $search . ' from:' . $screen , 'showuser' => true );

				if( $result )
				{
					$params[ 'since_id' ]	= $result->id_str;
				}

				$data 		= $consumer->get('search', $params);

				$tweets		= isset( $data->results ) ? $data->results : '';

				foreach( $tweets as $tweet )
				{
					if( $tweet->from_user != $screen )
					{
						return;
					}

					// Remove hashtag from the content since it would be pointless to show it.
					$tweet->text	= str_ireplace( $hashes , '' , $tweet->text );
					$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
					$title		= JString::substr( $tweet->text , 0 , 20 ) . '...';
					$created	= JFactory::getDate( $tweet->created_at );
					$createdDate= $created->toMySQL();
					$content	= $tweet->text;

					// @task: Store the blog post
					$blog->set( 'title' 	, $title );
					$blog->set( 'source'	, 'twitter' );
					$blog->set( 'created_by', $account->user_id );
					$blog->set( 'created'	, $createdDate );
					$blog->set( 'modified'	, $createdDate );
					$blog->set( 'publish_up', $createdDate );
					$blog->set( 'intro'		, $content );
					$blog->set( 'category_id', $categoryId );
					$blog->set( 'published' , $published );
					$blog->set( 'frontpage'	, $frontpage );
					$blog->set( 'issitewide', true );
					// Store the blog post
					$blog->store();

					// @task: Add a history item
					$history	= EasyBlogHelper::getTable( 'TwitterMicroBlog' , 'Table' );
					$history->set( 'id_str' , $tweet->id_str );
					$history->set( 'post_id' , $blog->id );
					$history->set( 'oauth_id', $account->id );
					$history->set( 'created' , $createdDate );
					$history->set( 'tweet_author' , $screen );

					$history->store();

					$total++;
				}
			}
		}

		echo JText::sprintf( '%1s blog posts fetched from Twitter' , $total );
	}
}
