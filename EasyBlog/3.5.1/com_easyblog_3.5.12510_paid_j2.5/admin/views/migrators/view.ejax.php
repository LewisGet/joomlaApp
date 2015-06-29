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
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.utilities.simplexml' );
jimport( 'joomla.utilities.arrayhelper' );

require_once( EBLOG_HELPERS . DS . 'helper.php' );
require_once( EBLOG_HELPERS . DS . 'string.php' );
require_once( EBLOG_HELPERS . DS . 'image.php' );
//require_once( EBLOG_HELPERS . DS . 'date.php' );

class EasyBlogViewMigrators extends JView
{
	var $err				= null;

	function migrateArticle($params)
	{

		$post	= EasyBlogStringHelper::ejaxPostToArray($params);

		if(isset($post['com_type']))
		{

			$migrateStat    = new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->comments	= 0;
			$migrateStat->images	= 0;
			$migrateStat->user      = array();

			$jSession = JFactory::getSession();
			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');

			$com_type   = $post['com_type'];

			switch($com_type)
			{
			    case 'com_blog':

					$migrateComment	= isset($post['smartblog_comment']) ? $post['smartblog_comment'] : '0';
					$migrateImage	= isset($post['smartblog_image']) ? $post['smartblog_image'] : '0';
					$imagePath		= isset($post['smartblog_imagepath']) ? $post['smartblog_imagepath'] : '';

					$this->_processSmartBlog($migrateComment, $migrateImage, $imagePath);

			        break;
			    case 'com_content':

					$authorId	= isset($post['authorId']) ? $post['authorId'] : '0';
					$stateId	= isset($post['stateId']) ? $post['stateId'] : '*';
					$catId		= isset($post['catId']) ? $post['catId'] : '0';
					$sectionId	= isset($post['sectionId']) ? $post['sectionId'] : '-1';
					$start		= 1;
					$myblogSection   = isset($post['$myblogSection']) ? $post['$myblogSection'] : '';

					$jomcomment		= isset( $post['content-jomcomment'] ) ? true : false;
					$this->_process($authorId, $stateId, $catId, $sectionId, $myblogSection , $jomcomment );

			        break;
			    case 'com_lyftenbloggie':
			    	//migrate lyftenbloggie tags
			    	$migrateComment	= isset($post['lyften_comment']) ? $post['lyften_comment'] : '0';

					$this->_migrateLyftenTags();
			        $this->_processLyftenBloggie( $migrateComment );
			        break;
			    case 'com_myblog':

		        	require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_myblog' . DS . 'config.myblog.php');
		        	$myblogConfig	= new MYBLOG_Config();
		        	$myBlogSection	= $myblogConfig->get('postSection');

			    	$jomcomment		= isset( $post['myblog-jomcomment'] ) ? true : false;
			        $this->_processMyBlog( $myBlogSection , $jomcomment );
			        break;
			    case 'com_wordpress':

					$wpBlogId	= isset($post['wpBlogId']) ? $post['wpBlogId'] : '-1';

			        $this->_processWordPress( $wpBlogId );
			        break;
			    case 'xml_wordpress':

			        $fileName   = $post['wpxmlfiles'];
			        $authorId   = $post['authorid'];

			        $this->_processWordPressXML( $fileName, $authorId );
					break;

			    default:
			        break;

			}

		}

	}

	public function migrateK2( $params , $migrateComments = false )
	{

		if( !is_array( $params ) )
		{
			$data	= array( 'k2category' => $params );
		}
		else
		{
			$data	= EasyBlogStringHelper::ejaxPostToArray($params);
		}

		$db					= JFactory::getDBO();
	    $jSession 			= JFactory::getSession();
		$ejax				= new EJax();

		if( isset( $data['migrate_k2_comments'] ) )
		{
			$migrateComments	= true;
		}

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');

		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

		$k2Category		= $data[ 'k2category' ];

		$query	= 'SELECT * FROM `#__k2_items` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote('com_k2');
		$query	.= ' )';
		$query	.= ' AND a.`catid` = ' . $db->Quote($k2Category);
		$query	.= ' ORDER BY a.`id` LIMIT 1';

		$db->setQuery($query);
		$row	= $db->loadObject();

		if(is_null($row))
		{

			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status-k2', '... finished.');
			$ejax->script("scrollToBottomK2();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= 'Total blog posts migrated : ' . $migrateStat->blog . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= 'Total user\'s contribution: ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= 'Total blog post from user \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status-k2', $stat);

			$ejax->script("$( '#migrator-submit-k2' ).html('Migration completed.');");
			$ejax->script("$( '#migrator-submit-k2' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait-k2' ).css( 'display' , 'none' );");

		}
		else
		{
			// here we should process the migration

			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.

			$date           = JFactory::getDate();
			$blogObj    	= new stdClass();

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			if(! empty($row->catid))
			{

			    $joomlaCat  = $this->_getK2Category($row->catid);

			    $eCat   	= $this->_isEblogCategoryExists($joomlaCat);

				if($eCat === false)
				{
				    $eCat   = $this->_createEblogCategory($joomlaCat);
				}

				$blogObj->category_id   = $eCat;
			}

			$profile	= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$blog		= EasyBlogHelper::getTable( 'Blog', 'Table' );

			//load user profile
			$profile->load( $row->created_by );

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $row->created ) ? $row->created : $date->toMySQL();
			$blogObj->modified		= $date->toMySQL();

			$blogObj->title			= $row->title;
			$blogObj->permalink		= ( empty($row->alias) ) ? EasyBlogHelper::getPermalink($row->title) : $row->alias;

			if(empty($row->fulltext))
			{
				$blogObj->intro			= $row->introtext;
			}
			else
			{
				$blogObj->intro			= $row->introtext;
				$blogObj->content		= $row->fulltext;
			}

			//translating the article state into easyblog publish status.
			$blogState  = '';

			// Since K2 does not have `state` column, we need to map it back.
			$row->state	= $row->published;

			if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
			{
			    $blogState  = ($row->state == 2 || $row->state == -2) ? 0 : $row->state;
			}
			else
			{
			    $blogState  = ($row->state == -1) ? 0 : $row->state;
			}

			$blogObj->published		= $blogState;
			$blogObj->publish_up 	= !empty( $row->publish_up )? $row->publish_up : $date->toMySQL();
			$blogObj->publish_down	= !empty( $row->publish_down )? $row->publish_down : $date->toMySQL();

			$blogObj->ordering		= $row->ordering;
			$blogObj->hits			= $row->hits;
			$blogObj->frontpage     = 1;

			$blog->bind($blogObj);

			// Migrate K2 Images
			$this->_migrateK2Images( $row , $blog , $profile );


			$blog->store();

			//migrate meta description
			$this->_migrateContentMeta($row->metakey, $row->metadesc, $blog->id);

			// Map K2 tags into EasyBlog tags
			$query	= 'SELECT a.* FROM #__k2_tags AS a '
					. 'INNER JOIN #__k2_tags_xref AS b '
					. 'ON a.`id`=b.`tagID` '
					. 'WHERE b.`itemID`=' . $db->Quote( $row->id );
			$db->setQuery($query);

			$k2Tags	= $db->loadObjectList();

			if( $k2Tags )
			{
				foreach( $k2Tags as $item )
				{
				    $now    = JFactory::getDate();
					$tag	= EasyBlogHelper::getTable( 'Tag', 'Table' );


					if( $tag->exists( $item->name ) )
					{
					    $tag->load( $item->name, true);
					}
					else
					{
						// Create tag if necessary
					    $tagArr = array();
					    $tagArr['created_by']  	= $this->_getSAUserId();
					    $tagArr['title']  		= $item->name;
					    $tagArr['alias']  		= $item->name;
					    $tagArr['published']  	= '1';
					    $tagArr['created']     	= $now->toMySQL();

                        $tag->bind($tagArr);
					    $tag->store();
					}

					$postTag	= EasyBlogHelper::getTable( 'PostTag', 'Table' );
					$postTag->tag_id	= $tag->id;
					$postTag->post_id	= $blog->id;
					$postTag->created	= $now->toMySQL();
					$postTag->store();
				}
			}


			//update session value
			$migrateStat->blog++;
			$statUser   	= $migrateStat->user;
			$statUserObj    = null;
			if(! isset($statUser[$profile->id]))
			{
			    $statUserObj    = new stdClass();
			    $statUserObj->name  		= $profile->nickname;
			    $statUserObj->blogcount		= 0;
			}
			else
			{
			    $statUserObj    = $statUser[$profile->id];
			}
			$statUserObj->blogcount++;
			$statUser[$profile->id] = $statUserObj;
			$migrateStat->user  	= $statUser;


			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');

			//log the entry into migrate table.
			$migrator = EasyBlogHelper::getTable( 'Migrate', 'Table' );

			$migrator->content_id	= $row->id;
			$migrator->post_id		= $blog->id;
			$migrator->session_id	= $jSession->getToken();
			$migrator->component    = 'com_k2';
			$migrator->store();

			$ejax->script( '$("#no-progress-k2").hide();' );
			$ejax->append('progress-status-k2', 'Migrated K2 item :' . $row->id . ' into EasyBlog with blog id:' . $blog->id . '<br />');

			if( $migrateComments )
			{
				$this->migrateK2Comments( $row , $blog );
				$ejax->append('progress-status-k2', 'Migrated K2 Comments for K2 Item :' . $row->id . ' into EasyBlog Comments with blog id:' . $blog->id . '<br />');

				$return 	= array();
				$return['migrate_k2_comments' ] = 1;
				$return['k2category']			= $k2Category;

				require_once( EBLOG_CLASSES . DS . 'json.php' );

				$json 		= new Services_JSON();
				$return 	= $json->encode( $return );

				$ejax->script("ejax.load('migrators','migrateK2','" . $k2Category . "' , '1');");
			}
			else
			{
				$ejax->script("ejax.load('migrators','migrateK2','" . $k2Category . "');");
			}
		}

		$ejax->send();
		exit;
	}

	public function _migrateK2Images( &$row , &$blog , $author )
	{
		jimport( 'joomla.filesystem.file' );

		$name	= md5( 'Image' . $row->id );
		$path	= JPATH_ROOT . DS . 'media' . DS . 'k2' . DS . 'items' . DS . 'src' . DS . $name . '.jpg';
		$config	= EasyBlogHelper::getConfig();
		$configStorage		= str_ireplace( '\\' , DS , $config->get( 'main_image_path' ) );
		$newPath			= JPATH_ROOT . DS . $configStorage . DS . $author->id;

		if( !JFolder::exists( $newPath ) )
		{
			JFolder::create( $newPath );
		}

		if( JFile::exists( $path ) )
		{


			// Copy the full scaled image
			$large				= JPATH_ROOT . DS . 'media' . DS . 'k2' . DS . 'items' . DS . 'cache' . DS . $name . '_XL.jpg';
			$targetLarge		= $newPath . DS . $name . '.jpg';
			$largeSrc			= rtrim( JURI::root() , '/' ) . '/' . str_ireplace( '\\' , '/' , $configStorage ) . '/' . $author->id . '/' . $name . '.jpg';
			@JFile::copy( $large , $targetLarge );

			$medium				= JPATH_ROOT . DS . 'media' . DS . 'k2' . DS . 'items' . DS . 'cache' . DS . $name . '_M.jpg';
			$targetMedium		= $newPath . DS . EBLOG_MEDIA_THUMBNAIL_PREFIX . $name . '.jpg';
			$mediumSrc			= rtrim( JURI::root() , '/' ) . '/' . str_ireplace( '\\' , '/' , $configStorage ) . '/' . $author->id . '/' . EBLOG_MEDIA_THUMBNAIL_PREFIX . $name . '.jpg';
			@JFile::copy( $medium , $targetMedium );

			// Now we need to add the new image data into the content since K2 does not store them in the content.
			$blog->intro		.= '<div style="text-align: center;"><a class="easyblog-thumb-preview" href="' . $largeSrc . '"><img src="' . $mediumSrc . '" /></a></div>';
		}
	}

	/* adapted from wordpress importer */
	function _processWordPressXML( $fileName, $authorId )
	{
	    $jSession 	= JFactory::getSession();

	    $fixedLocation  = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_easyblog' . DS . 'xmlfiles';
		$file 			= $fixedLocation . DS . $fileName;

		if( JFile::exists( $file ) )
		{
			/* use for debugging - dun remove */
			/*
			libxml_use_internal_errors(true);
			var_dump( simplexml_load_file( $file ) );
		    $errors = libxml_get_errors();
			var_dump($errors);
		    foreach ($errors as $error) {
				var_dump($error);
		    }
		    libxml_clear_errors();
			exit;
			*/

   			$parser = simplexml_load_file( $file );

			if( $parser )
			{

				$baseUrl = $parser->xpath('/rss/channel/wp:base_site_url');
				$baseUrl = (string) trim( $baseUrl[0] );

				$namespaces = $parser->getDocNamespaces();

				if ( ! isset( $namespaces['wp'] ) )
				{
					$namespaces['wp'] = 'http://wordpress.org/export/1.1/';
				}

				if ( ! isset( $namespaces['excerpt'] ) )
				{
					$namespaces['excerpt'] = 'http://wordpress.org/export/1.1/excerpt/';
				}

				$posts  		= array();
				$attachments    = array();

				// process each items
				foreach ( $parser->channel->item as $item )
				{
					$post = array(
						'post_title' => (string) $item->title,
						'guid' => (string) $item->guid,
						'link' => (string) $item->link,
					);

					$dc = $item->children( 'http://purl.org/dc/elements/1.1/' );
					$post['post_author'] = (string) $dc->creator;

					$content = $item->children( 'http://purl.org/rss/1.0/modules/content/' );
					$excerpt = $item->children( $namespaces['excerpt'] );

					$post['post_content'] = (string) $content->encoded;
					$post['post_excerpt'] = (string) $excerpt->encoded;

					$wp = $item->children( $namespaces['wp'] );

					$post['post_id'] 		= (int) $wp->post_id;
					$post['post_date_gmt'] 	= (string) $wp->post_date_gmt;

					$post['comment_status'] = (string) $wp->comment_status;

					$post['post_name'] 		= (string) $wp->post_name;
					$post['status'] 		= (string) $wp->status; // publish , draft

					$post['post_type'] 		= (string) $wp->post_type;
					$post['post_parent']	= (string) $wp->post_parent;
					$post['post_password'] 	= (string) $wp->post_password;
					$post['attachment_url'] = (string) $wp->attachment_url;


					if( ($post['post_type'] != 'post') && ($post['post_type'] != 'attachment') )
					    continue;


					foreach ( $item->category as $terms )
					{
						$att = $terms->attributes();
						if ( isset( $att['nicename'] ) )
						{
							$post['terms'][] = array(
								'name' => (string) $terms,
								'slug' => (string) $att['nicename'],
								'domain' => (string) $att['domain']
							);
						}
					}

					foreach ( $wp->postmeta as $meta )
					{
						$post['postmeta'][] = array(
							'key' => (string) $meta->meta_key,
							'value' => (string) $meta->meta_value
						);
					}

					$postComments   = array();
					foreach ( $wp->comment as $comment )
					{
						if( empty( $comment->comment_content ) )
							continue;

						$postComments[] = array(
							'comment_id' => (int) $comment->comment_id,
							'comment_author' => (string) $comment->comment_author,
							'comment_author_email' => (string) $comment->comment_author_email,
							'comment_author_IP' => (string) $comment->comment_author_IP,
							'comment_author_url' => (string) $comment->comment_author_url,
							'comment_date' => (string) $comment->comment_date,
							'comment_date_gmt' => (string) $comment->comment_date_gmt,
							'comment_content' => (string) $comment->comment_content,
							'comment_approved' => (string) $comment->comment_approved,
							'comment_type' => (string) $comment->comment_type,
							'comment_parent' => (string) $comment->comment_parent,
							'comment_user_id' => (int) $comment->comment_user_id
						);
					}//end foreach

					if( $post['post_type'] == 'attachment' )
					{
					    $post_parant    = $post['post_parent'];
					    $this->logXMLData( $fileName,  $post_parant, 'attachment',  $post );
					}
					else
					{
					    $post_id    = $post['post_id'];

						if( count($postComments) > 1000 )
						{
							$postComments = array_slice($postComments, 0, 1000);
						}

					    $this->logXMLData( $fileName,  $post_id, 'post',  $post, $postComments );
					}

				} //end foreach

				$this->_migrateWPXML( $fileName, $authorId );
			}
			else
			{
			    $ejax		= new EJax();
			    $ejax->append('progress-status6', '... error parsing xml file. Please try again later.');
				$ejax->script("$( '#migrator-submit6' ).attr('disabled' , '');");
				$ejax->script("$( '#icon-wait6' ).css( 'display' , 'none' );");
				$ejax->send();
			}// if parser
		}
		else
		{
		    $ejax		= new EJax();
		    $ejax->append('progress-status6', '... error reading xml file. Please try again later.');
			$ejax->script("$( '#migrator-submit6' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait6' ).css( 'display' , 'none' );");
			$ejax->send();
		}
	}

	function logXMLData( $fileName,  $postId, $source,  $data, $comments = array() )
	{
	    $jSession 	= JFactory::getSession();

		//log the entry into migrate table.
		$xml = EasyBlogHelper::getTable( 'Xmldata', 'Table' );

		$xml->post_id		= $postId;
		$xml->session_id	= $jSession->getToken();
		$xml->source    	= $source;
		$xml->filename    	= $fileName;
		$xml->data			= serialize($data);
		$xml->comments		= (! empty($comments) ) ? serialize($comments) : '';

		$xml->store();
	}

	function getXMLPostData( $fileName )
	{
	    $jSession 	= JFactory::getSession();
	    $db         =& JFactory::getDBO();

	    $sessId     = $jSession->getToken();

	    $query  = 'select * from `#__easyblog_xml_wpdata`';
 	    $query  .= ' where `session_id` = ' . $db->Quote( $sessId );
 	    $query  .= ' and `filename` = ' . $db->Quote($fileName);
	    $query  .= ' and `source` = ' . $db->Quote('post');
	    $query  .= ' order by `id` limit 1';

	    $db->setQuery($query);
	    $result = $db->loadObject();

	    $contentId  = '';
	    if( isset($result->post_id) )
	    {
	        $contentId  = $result->post_id;
	        $result->data 		= unserialize($result->data);
			if( !empty($result->comments) )
				$result->comments 	= unserialize($result->comments);
	    }

		$this->clearXMLData( $fileName, $contentId);

	    return $result;
	}

	function getXMLAttachmentData( $fileName, $postid )
	{
	    $jSession 	= JFactory::getSession();
	    $db         = JFactory::getDBO();

	    $sessId     = $jSession->getToken();

	    $query  = 'select * from `#__easyblog_xml_wpdata`';
	    $query  .= ' where `session_id` = ' . $db->Quote( $sessId );
	    $query  .= ' and `filename` = ' . $db->Quote($fileName);
	    $query  .= ' and `source` = ' . $db->Quote('attachment');
	    $query  .= ' and `post_id` = ' . $db->Quote($postid);

	    $db->setQuery($query);

	    $result = $db->loadObjectList();

	    $attachments    = array();

		if( count( $result ) > 0 )
		{
		    foreach( $result as $att)
		    {
		        $attachments[]  = unserialize( $att->data );
		    }
		}

	    return $attachments;
	}

	function clearXMLData( $fileName, $postid = '' )
	{
	    $jSession 	= JFactory::getSession();
	    $db         = JFactory::getDBO();

	    $sessId     = $jSession->getToken();


		if( $postid === true )
		{
		    $query  = 'delete from `#__easyblog_xml_wpdata`';
		    $query  .= ' where `session_id` = ' . $db->Quote( $sessId );
		    $query  .= ' and `filename` = ' . $db->Quote($fileName);
			$query  .= ' limit 1';
		}
		else
		{
		    $query  = 'delete from `#__easyblog_xml_wpdata`';
		    $query  .= ' where `session_id` = ' . $db->Quote( $sessId );
		    $query  .= ' and `filename` = ' . $db->Quote($fileName);
		    if( !empty( $postid ) )
		    	$query  .= ' and `post_id` = ' . $db->Quote($postid);
		}

	    $db->setQuery($query);
	    $db->query();

		return true;
	}

	function _migrateWPXML( $fileName, $authorId )
	{
	    $db			= JFactory::getDBO();
	    $jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

		$posts	= $this->getXMLPostData( $fileName );

		if( ! isset($posts->post_id) )
		{
		    $this->clearXMLData( $fileName );
			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status6', '... finished.');
			$ejax->script("divSrolltoBottomWordPressXML();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= 'Total blog posts migrated : ' . $migrateStat->blog . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= 'Total user\'s contribution: ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= 'Total blog post from user \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status6', $stat);

			$ejax->script("$( '#migrator-submit6' ).html('Finished. Click here to re-run the process again.');");
			$ejax->script("$( '#migrator-submit6' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait6' ).css( 'display' , 'none' );");
			$ejax->send();
		}

		$data   	= $posts->data;
		$contentId 	= $data['post_id'];

		$data['comments']   = $posts->comments;

		if( empty( $contentId ) )
		{
			$this->clearXMLData( $fileName, true);
			$ejax->script("ejax.load('migrators','_migrateWPXML','$fileName','$authorId');");
			$ejax->send();
		}

		$query	= 'SELECT content_id FROM `#__easyblog_migrate_content` AS b';
		$query	.= ' WHERE b.`content_id` = '. $db->Quote( $contentId );
		$query	.= '  and `component` = ' . $db->Quote( 'xml_wordpress' );
		$query	.= '  and `filename` = ' . $db->Quote( $fileName );

		$db->setQuery($query);
		$row	= $db->loadResult();

		if( is_null( $row ) )
		{
			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.
			$date           = JFactory::getDate();
			$blogObj    	= new stdClass();
			$adminId        = ( empty($authorId) ) ? EasyBlogHelper::getDefaultSAIds() : $authorId;

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			$wpCat  = '';
			$wpTag  = array();

			if( ! empty($data['terms']) )
			{
			    foreach( $data['terms'] as $term )
				{
				    if( $term['domain'] == 'category' && empty($wpCat) )
				    {
				        $wpCat  = new stdClass();
				        $wpCat->title  = $term['name'];
				        $wpCat->alias  = $term['slug'];
				    }
				    else if( $term['domain'] == 'post_tag' )
				    {
				        $tmpTag = new stdClass();
				        $tmpTag->title  = $term['name'];
				        $tmpTag->alias  = $term['slug'];
				        $wpTag[] = $tmpTag;
				    }
				}
			}

			if(isset($wpCat->title))
			{
			    $eCat   	= $this->_isEblogCategoryExists($wpCat);
				if($eCat === false)
				{
				    $eCat   = $this->_createEblogCategory($wpCat);
				}

				$blogObj->category_id   = $eCat;
			}

			$profile	= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$blog		= EasyBlogHelper::getTable( 'Blog', 'Table' );

			//load user profile
			$profile->load( $adminId );

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $data['post_date_gmt'] ) ? $data['post_date_gmt'] : $date->toMySQL();
			$blogObj->modified		= $date->toMySQL();

			$blogObj->title			= $data['post_title'];
			$blogObj->permalink		= ( empty($data['post_name']) ) ? EasyBlogHelper::getPermalink($data['post_title']) : $data['post_name'];

			// Migrate caption
			$pattern2   = '/\[caption.*caption="(.*)"\]/iU';
            $data['post_content'] 	= preg_replace( $pattern2 , '<div class="caption">$1</div>' , $data['post_content'] );
            $data['post_content']	= str_ireplace( '[/caption]' , '<br />' , $data['post_content'] );

			// process attachments.
// 			$attachments    = $this->getXMLAttachmentData( $fileName, $contentId);
// 			if( count($attachments) > 0 )
// 			{
// 				$data['post_excerpt'] = $this->_processWPXMLAttachment($contentId, $data['post_excerpt'], $attachments, $authorId);
// 				$data['post_content'] = $this->_processWPXMLAttachment($contentId, $data['post_content'], $attachments, $authorId);
// 			}

			$data['post_excerpt']	= nl2br( $data['post_excerpt'] );
			$data['post_content']	= nl2br( $data['post_content'] );

			$blogObj->intro			= $data['post_excerpt'];
			$blogObj->content		= $data['post_content'];

			//translating the article state into easyblog publish status.
			$blogState  	= '0';
			$isPrivate		= '0';
			if( $data['status'] == 'private' )
			{
                $isPrivate  = '1';
                $blogState  = '1';
			}
			else if( $data['status'] == 'publish' )
			{
                $isPrivate  = '0';
                $blogState  = '1';
			}

			$blogObj->blogpassword  = $data['post_password'];
			$blogObj->private       = $isPrivate;
			$blogObj->published		= $blogState;
			$blogObj->publish_up 	= !empty( $data['post_date_gmt'] )? $data['post_date_gmt'] : $date->toMySQL();
			$blogObj->publish_down	= '0000-00-00 00:00:00';

			$blogObj->ordering		= 0;
			$blogObj->hits			= 0;
			$blogObj->frontpage     = 1;
			$blogObj->allowcomment  = ($data['comment_status'] == 'open') ? 1 : 0;

			$blog->bind($blogObj);
			$blog->store();

			// add tags.
			if( count($wpTag) > 0)
			{

			    foreach($wpTag as $item)
			    {
				    $now    = JFactory::getDate();
					$tag	= EasyBlogHelper::getTable( 'Tag', 'Table' );

					if( $tag->exists( $item->title ) )
					{
					    $tag->load( $item->title, true);
					}
					else
					{
					    $tagArr = array();
					    $tagArr['created_by']  	= $adminId;
					    $tagArr['title']  		= $item->title;
					    $tagArr['alias']  		= $item->alias;
					    $tagArr['published']  	= '1';
					    $tagArr['created']     	= $now->toMySQL();

                        $tag->bind($tagArr);
					    $tag->store();
					}

					$postTag	= EasyBlogHelper::getTable( 'PostTag', 'Table' );
					$postTag->tag_id	= $tag->id;
					$postTag->post_id	= $blog->id;
					$postTag->created	= $now->toMySQL();
					$postTag->store();

			    }
			}

			// add comments

			if( isset($data['comments']) )
			{

				if( is_array($data['comments']) && count($data['comments']) > 0)
				{
				    $next   = array();
				    $next['lft'] = 1;
				    $next['rgt'] = 2;

					for($cnt = 0; $cnt < count( $data['comments'] ); $cnt++ )
					{

					    $item	= JArrayHelper::toObject( $data['comments'][$cnt] );
					    $next	= $this->_migrateWPComments('xml', $contentId, $blog->id, '0', $item, $next);
					}
				}
			}

			//update session value
			$migrateStat->blog++;
			$statUser   	= $migrateStat->user;
			$statUserObj    = null;
			if(! isset($statUser[$profile->id]))
			{
			    $statUserObj    = new stdClass();
			    $statUserObj->name  		= $profile->nickname;
			    $statUserObj->blogcount		= 0;
			}
			else
			{
			    $statUserObj    = $statUser[$profile->id];
			}
			$statUserObj->blogcount++;
			$statUser[$profile->id] = $statUserObj;
			$migrateStat->user  	= $statUser;

			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');


			//log the entry into migrate table.
			$migrator = EasyBlogHelper::getTable( 'Migrate', 'Table' );

			$migrator->content_id	= $contentId;
			$migrator->post_id		= $blog->id;
			$migrator->session_id	= $jSession->getToken();
			$migrator->component    = 'xml_wordpress';
			$migrator->filename    	= $fileName;
			$migrator->store();

			//$this->clearXMLData( $fileName, $contentId);

			$ejax->append('progress-status6', 'Migrated WordPress XML blog post :' . $contentId . ' into EasyBlog with blog id:' . $blog->id . '<br />');
		    $ejax->script("ejax.load('migrators','_migrateWPXML','$fileName','$authorId');");
		}
		else
		{
		    // skip, go to next item.
		    $ejax->script("ejax.load('migrators','_migrateWPXML','$fileName','$authorId');");
		}

		$ejax->send();
	}

	function _processWPXMLAttachment( $wpPostId, $content, $attachments, $authorId)
	{
	    require_once( EBLOG_HELPERS . DS . 'connectors.php' );

		foreach( $attachments as $attachment)
		{
			$link    		= $attachment['link'];
			$attachementURL = $attachment['attachment_url'];

			if( EasyImageHelper::isImage($attachementURL) )
			{
			    $filname    = EasyImageHelper::getFileName($attachementURL);
			    $extension  = EasyImageHelper::getFileExtension($attachementURL);

			    $folder   = JPATH_ROOT . DS . 'images' . DS . 'blogs' . DS . $wpPostId;
			    if( !JFolder::exists( $folder ) )
			    {
			    	JFolder::create( $folder );
				}

				// new image location
				$newFile    = $folder . DS . $filname;

			    $connector  = new EasyBlogConnectorsHelper();
				$connector->addUrl( $attachementURL );
				$connector->execute();
			    $imageraw	= $connector->getResult( $attachementURL );

			    if( $imageraw )
			    {
			        if( JFile::write($newFile, $imageraw ) )
			        {
					    //replace the string in the content.
					    $absImagePath   = rtrim( JURI::root(), '/' ) . '/images/blogs/' . $wpPostId . '/' . $filname;
					    $content		= str_ireplace( 'href="' . $link . '"'  , 'href="' . $absImagePath . '"' , $content );

					    $pattern 		= '/src=[\"\']?([^\"\']?.*(png|jpg|jpeg|gif))[\"\']?/i';
					    $content		= preg_replace( $pattern  , 'src="'.$absImagePath.'"' , $content );
			        }
				}

// 				if( file_put_contents( $newFile, file_get_contents($attachementURL) ) !== false )
// 				{
// 				    //replace the string in the content.
// 				    $absImagePath   = rtrim( JURI::root(), '/' ) . '/images/blogs/' . $wpPostId . '/' . $filname;
// 				    $content		= JString::str_ireplace( 'href="' . $link . '"'  , 'href="' . $absImagePath . '"' , $content );
//
// 				    $pattern 		= '/src=[\"\']?([^\"\']?.*(png|jpg|jpeg|gif))[\"\']?/i';
// 				    $content		= preg_replace( $pattern  , 'src="'.$absImagePath.'"' , $content );
// 				}
			}
		}

		return $content;
	}

	function _processWordPress( $wpBlogId )
	{
	    $db			= JFactory::getDBO();
	    $jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

		$wpTableNamePrex    = ($wpBlogId == '1') ? '' : $wpBlogId . '_';
		$wpComponentName    = 'com_wordpress' . $wpBlogId;


		$query	= 'SELECT a.`ID` as `id`, a.* FROM `#__wp_' . $wpTableNamePrex . 'posts` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote( $wpComponentName );
		$query	.= ' )';
		$query  .= ' AND `post_type` = ' . $db->Quote( 'post' );
		$query  .= ' AND `post_status` != ' . $db->Quote( 'auto-draft' );
		$query	.= ' ORDER BY a.`id` LIMIT 1';


		$db->setQuery($query);
		$row	= $db->loadObject();

		if(is_null($row))
		{

			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status5', '... finished.');
			$ejax->script("divSrolltoBottomWordPress();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= 'Total blog posts migrated : ' . $migrateStat->blog . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= 'Total user\'s contribution: ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= 'Total blog post from user \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status5', $stat);

			$ejax->script("$( '#migrator-submit5' ).html('Finished. Click here to re-run the process again.');");
			$ejax->script("$( '#migrator-submit5' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait5' ).css( 'display' , 'none' );");

		}
		else
		{
			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.

			$date           = JFactory::getDate();
			$blogObj    	= new stdClass();

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			$wpCat  = $this->_getWPTerms( $wpTableNamePrex, $row->id, 'category');
			if(isset($wpCat->title))
			{
			    $eCat   	= $this->_isEblogCategoryExists($wpCat);
				if($eCat === false)
				{
				    $eCat   = $this->_createEblogCategory($wpCat);
				}

				$blogObj->category_id   = $eCat;
			}

			$profile	= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$blog		= EasyBlogHelper::getTable( 'Blog', 'Table' );

			//load user profile
			$profile->load( $row->post_author );

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $row->post_date ) ? $row->post_date : $date->toMySQL();
			$blogObj->modified		= $date->toMySQL();

			$blogObj->title			= $row->post_title;
			$blogObj->permalink		= ( empty($row->post_name) ) ? EasyBlogHelper::getPermalink($row->post_title) : $row->post_name;

			/* replacing [caption] and [gallery] */

			// Migrate caption
			$pattern2   = '/\[caption.*caption="(.*)"\]/iU';
            $row->post_content  = preg_replace( $pattern2 , '<div class="caption">$1</div>' , $row->post_content );
            $row->post_content	= str_ireplace( '[/caption]' , '<br />' , $row->post_content );

			// Migrate galleries
			$pattern	= '/\[gallery(.*)/i';
			preg_match( $pattern , $row->post_content , $matches );
			if( !empty( $matches ) )
			{
			    $folder   = JPATH_ROOT . DS . 'images' . DS . 'blogs' . DS . $row->id;
			    if( !JFolder::exists( $folder ) )
			    {
			    	JFolder::create( $folder );
				}

			    // Now fetch items
				$query	= 'SELECT a.guid FROM `#__wp_' . $wpTableNamePrex . 'posts` AS a';
				$query  .= ' WHERE `post_type` = ' . $db->Quote( 'attachment' );
				$query  .= ' AND `post_mime_type` LIKE "%image%"';
				$query	.= ' AND `post_parent`=' . $db->Quote( $row->id );

				//http://maephim.se/piccolina/wp-content/uploads/2011/04/Thailand-Apr-2010-080-Large.jpg
				//http://easyblog.localhost.com/components/com_wordpress/wp/wp-content/uploads/2011/08/262131_1791775084596_1546222768_31409359_5180181_n.jpg-540×720-pixels.jpg
				$db->setQuery( $query );
				$cibais	= $db->loadObjectList();

				$images 	= array();
				$siteRoot   = JURI::root();

				foreach( $cibais as $cibai )
				{
				    $image  = $cibai->guid;

					$image  = str_ireplace( $siteRoot , '' , $image );
					$image  = str_ireplace( '/' , DS , $image );

					$imageFull  = JPATH_ROOT . DS . $image;
					$parts= explode( DS , $imageFull );
					JFile::copy( $imageFull , JPATH_ROOT . DS . 'images' . DS . 'blogs' . DS  . $row->id . DS .  $parts[ count( $parts ) - 1 ] );
				}


				// Replace content with the proper gallery tag
				//{gallery}4745732{/gallery}
				$row->post_content	= JString::str_ireplace( $matches[0] , '{gallery}' . $row->id . '{/gallery}' , $row->post_content );
			}

			/* end replacing [caption] and [gallery] */

			$row->post_excerpt	= nl2br( $row->post_excerpt );
			$row->post_content	= nl2br( $row->post_content );

			$blogObj->intro			= $row->post_excerpt;
			$blogObj->content		= $row->post_content;


			//translating the article state into easyblog publish status.
			$blogState  	= '0';
			$isPrivate		= '0';
			if( $row->post_status == 'private' )
			{
                $isPrivate  = '1';
                $blogState  = '1';
			}
			else if( $row->post_status == 'publish' )
			{
                $isPrivate  = '0';
                $blogState  = '1';
			}

			$blogObj->blogpassword  = $row->post_password;
			$blogObj->private       = $isPrivate;
			$blogObj->published		= $blogState;
			$blogObj->publish_up 	= !empty( $row->post_date )? $row->post_date : $date->toMySQL();
			$blogObj->publish_down	= '0000-00-00 00:00:00';

			$blogObj->ordering		= 0;
			$blogObj->hits			= 0;
			$blogObj->frontpage     = 1;
			$blogObj->allowcomment  = ($row->comment_status == 'open') ? 1 : 0;

			$blog->bind($blogObj);
			$blog->store();

			// add tags.
			$wpPostTag  = $this->_getWPTerms( $wpTableNamePrex, $row->id, 'post_tag');
			if( count($wpPostTag) > 0)
			{

			    foreach($wpPostTag as $item)
			    {
				    $now    = JFactory::getDate();
					$tag	= EasyBlogHelper::getTable( 'Tag', 'Table' );

					if( $tag->exists( $item->title ) )
					{
					    $tag->load( $item->title, true);
					}
					else
					{
					    $tagArr = array();
					    $tagArr['created_by']  	= $this->_getSAUserId();
					    $tagArr['title']  		= $item->title;
					    $tagArr['alias']  		= $item->alias;
					    $tagArr['published']  	= '1';
					    $tagArr['created']     	= $now->toMySQL();

                        $tag->bind($tagArr);
					    $tag->store();
					}

					$postTag	= EasyBlogHelper::getTable( 'PostTag', 'Table' );
					$postTag->tag_id	= $tag->id;
					$postTag->post_id	= $blog->id;
					$postTag->created	= $now->toMySQL();
					$postTag->store();

			    }
			}


			// add comments
			$query	= 'SELECT * FROM `#__wp_' . $wpTableNamePrex . 'comments` AS a';
			$query	.= ' where `comment_post_ID` = ' . $db->Quote( $row->id );
			$query  .= ' and `comment_approved` = ' . $db->Quote('1');
			$query  .= ' and `comment_parent` = ' . $db->Quote('0');
			$query  .= ' order by `comment_date` ASC';

			$db->setQuery($query);
			$result = $db->loadObjectList();

			if( count($result) > 0)
			{
			    $next   = array();
			    $next['lft'] = 1;
			    $next['rgt'] = 2;

			    foreach( $result as $item)
			    {
			        $next	= $this->_migrateWPComments($wpTableNamePrex, $row->id, $blog->id, '0', $item, $next);
			    }
			}
		    //end adding comments



			//update session value
			$migrateStat->blog++;
			$statUser   	= $migrateStat->user;
			$statUserObj    = null;
			if(! isset($statUser[$profile->id]))
			{
			    $statUserObj    = new stdClass();
			    $statUserObj->name  		= $profile->nickname;
			    $statUserObj->blogcount		= 0;
			}
			else
			{
			    $statUserObj    = $statUser[$profile->id];
			}
			$statUserObj->blogcount++;
			$statUser[$profile->id] = $statUserObj;
			$migrateStat->user  	= $statUser;


			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');


			//log the entry into migrate table.
			$migrator = EasyBlogHelper::getTable( 'Migrate', 'Table' );

			$migrator->content_id	= $row->id;
			$migrator->post_id		= $blog->id;
			$migrator->session_id	= $jSession->getToken();
			$migrator->component    = $wpComponentName;
			$migrator->store();

			$ejax->append('progress-status5', 'Migrated WordPress blog post :' . $row->id . ' into EasyBlog with blog id:' . $blog->id . '<br />');
			$ejax->script("ejax.load('migrators','_processWordPress','$wpBlogId');");

		}

		$ejax->send();
	}

	function _migrateWPComments($wpTableNamePrex, $postId, $blogId, $parentId, $item, $next)
	{
		$now	= JFactory::getDate();
		$db		= JFactory::getDBO();
		$commt	= EasyBlogHelper::getTable( 'Comment', 'Table' );

		//we need to rename the esname and esemail back to name and email.
		$post               = array();
		$post['name']		= ( isset( $item->comment_author ) ) ? $item->comment_author : '';
		$post['email']		= ( isset( $item->comment_author_email ) ) ? $item->comment_author_email : '';
		$post['id']     	= $blogId;
		$post['comment']    = ( isset( $item->comment_content ) ) ? $item->comment_content : '';
		$post['title']      = '';
        $post['url']        = ( isset( $item->comment_author_url ) ) ? $item->comment_author_url : '';
        $post['ip']        	= ( isset( $item->comment_author_IP ) ) ? $item->comment_author_IP : '';
		$commt->bindPost($post);

		$commt->created_by  = ( $wpTableNamePrex == 'xml' ) ? '0' : $item->user_id;
		$commt->created		= ( isset( $item->comment_date ) ) ? $item->comment_date : '';
		$commt->modified	= ( isset( $item->comment_date ) ) ? $item->comment_date : '';
		$commt->published   = 1;
		$commt->parent_id   = $parentId;
		$commt->sent        = 1;

		$latestCmmt	= $this->_getEasyBlogLatestComment($blogId, $parentId);
		if( $parentId != 0)
		{
			$parentCommt	= EasyBlogHelper::getTable( 'Comment', 'Table' );
			$parentCommt->load($parentId);

			//adding new child comment
			$next['lft']		= $parentCommt->lft + 1;
			$next['rgt']		= $parentCommt->lft + 2;
			$nodeVal			= $parentCommt->lft;

			if(! empty($latestCmmt))
			{
			 	$next['lft']		= $latestCmmt->rgt + 1;
			 	$next['rgt']		= $latestCmmt->rgt + 2;
			 	$nodeVal			= $latestCmmt->rgt;
			}

			$this->_updateEasyBlogCommentSibling($blogId, $nodeVal);

			$commt->lft	= $next['lft'];
			$commt->rgt	= $next['rgt'];
		}
		else
		{
			//adding new comment
			if(! empty($latestCmmt))
			{
			 	$next['lft']	= $latestCmmt->rgt + 1;
			 	$next['rgt']	= $latestCmmt->rgt + 2;

			 	$this->_updateEasyBlogCommentSibling($blogId, $latestCmmt->rgt);
			}

			$commt->lft	= $next['lft'];
			$commt->rgt	= $next['rgt'];
		}

		$commt->store();

		if( $wpTableNamePrex != 'xml' )
		{
			//check to see if there is any child comments or not.
			$query	= 'SELECT a.* FROM `#__wp_' . $wpTableNamePrex . 'comments` AS a';
			$query	.= ' where `comment_post_ID` = ' . $db->Quote( $postId );
			$query  .= ' and `comment_approved` = ' . $db->Quote('1');
			$query  .= ' and `comment_parent` = ' . $db->Quote ( $item->comment_ID );
			$query  .= ' order by `comment_date` ASC';

			$db->setQuery($query);
			$result = $db->loadObjectList();

			if( count($result) > 0)
			{
			    foreach( $result as $citem)
			    {
			        $next	= $this->_migrateWPComments($wpTableNamePrex, $postId, $blogId, $commt->id, $citem, $next);
			    }
			}
		}

        return $next;
	}

	function _updateEasyBlogCommentSibling($blogId, $nodeValue)
	{
		$db	= JFactory::getDBO();

		$query	= 'UPDATE `#__easyblog_comment` SET `rgt` = `rgt` + 2';
		$query	.= ' WHERE `rgt` > ' . $db->Quote($nodeValue);
		$query	.= ' AND `post_id` = ' . $db->Quote($blogId);
		$db->setQuery($query);
		$db->query();

		$query	= 'UPDATE `#__easyblog_comment` SET `lft` = `lft` + 2';
		$query	.= ' WHERE `lft` > ' . $db->Quote($nodeValue);
		$query	.= ' AND `post_id` = ' . $db->Quote($blogId);
		$db->setQuery($query);
		$db->query();
	}


	function _getEasyBlogLatestComment($blogId, $parentId = 0)
	{
		$db	= JFactory::getDBO();

		$query	= 'SELECT `id`, `lft`, `rgt` FROM `#__easyblog_comment`';
		$query	.= ' WHERE `post_id` = ' . $db->Quote($blogId);
		if($parentId != 0)
			$query	.= ' AND `parent_id` = ' . $db->Quote($parentId);
		else
		    $query	.= ' AND `parent_id` = ' . $db->Quote('0');
		$query	.= ' ORDER BY `lft` DESC LIMIT 1';

		$db->setQuery($query);
		$result	= $db->loadObject();

		return $result;
	}

	function _getWPTerms( $wpTBPrex = '', $postId, $type)
	{
	    $db		= JFactory::getDBO();

		$query   = 'select distinct a.`name` as `title`, a.`slug` as `alias`, 1 as `published` from `#__wp_'.$wpTBPrex.'terms` as a';
		$query  .= '  inner join `#__wp_term_'.$wpTBPrex.'taxonomy` as b on a.`term_id` = b.`term_id`';
		$query  .= '  inner join `#__wp_term_'.$wpTBPrex.'relationships` as c on b.`term_taxonomy_id` = c.`term_taxonomy_id`';
		$query  .= ' where c.`object_id` = ' . $db->Quote($postId);
		$query  .= ' and b.`taxonomy` = ' . $db->Quote($type);

		$db->setQuery($query);

		$result = '';
		if( $type == 'category')
		{
			// always load one category bcos easyblog only support one category.
			$result = $db->loadObject();
		}
		else
		{
		    //tags
		    $result = $db->loadObjectList();
		}
	    return $result;
	}

	function _processMyBlog( $myBlogSection , $jomcomment = false )
	{
	    $db			= JFactory::getDBO();
	    $jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');

		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

		$query	= 'SELECT * FROM `#__content` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote('com_myblog');
		$query	.= ' )';
		$query	.= ' AND a.`sectionid` = ' . $db->Quote($myBlogSection);
		$query	.= ' ORDER BY a.`id` LIMIT 1';

		$db->setQuery($query);
		$row	= $db->loadObject();

		if(is_null($row))
		{

			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status4', '... finished.');
			$ejax->script("divSrolltoBottomMyblog();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= 'Total blog posts migrated : ' . $migrateStat->blog . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= 'Total user\'s contribution: ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= 'Total blog post from user \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status4', $stat);

			$ejax->script("$( '#migrator-submit4' ).html('Finished. Click here to re-run the process again.');");
			$ejax->script("$( '#migrator-submit4' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait4' ).css( 'display' , 'none' );");

		}
		else
		{
			// here we should process the migration

			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.

			$date           = JFactory::getDate();
			$blogObj    	= new stdClass();

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			if(! empty($row->catid))
			{

			    $joomlaCat  = $this->_getJoomlaCategory($row->catid);

			    $eCat   	= $this->_isEblogCategoryExists($joomlaCat);
				if($eCat === false)
				{
				    $eCat   = $this->_createEblogCategory($joomlaCat);
				}

				$blogObj->category_id   = $eCat;
			}

			$profile	= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$blog		= EasyBlogHelper::getTable( 'Blog', 'Table' );

			//load user profile
			$profile->load( $row->created_by );

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $row->created ) ? $row->created : $date->toMySQL();
			$blogObj->modified		= $date->toMySQL();

			$blogObj->title			= $row->title;
			$blogObj->permalink		= ( empty($row->alias) ) ? EasyBlogHelper::getPermalink($row->title) : $row->alias;

			if(empty($row->fulltext))
			{
				$blogObj->intro			= '';
				$blogObj->content		= $row->introtext;
			}
			else
			{
				$blogObj->intro			= $row->introtext;
				$blogObj->content		= $row->fulltext;
			}

			//translating the article state into easyblog publish status.
			$blogState  = '';
			if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
			{
			    $blogState  = ($row->state == 2 || $row->state == -2) ? 0 : $row->state;
			}
			else
			{
			    $blogState  = ($row->state == -1) ? 0 : $row->state;
			}

			$blogObj->published		= $blogState;
			$blogObj->publish_up 	= !empty( $row->publish_up )? $row->publish_up : $date->toMySQL();
			$blogObj->publish_down	= !empty( $row->publish_down )? $row->publish_down : $date->toMySQL();

			$blogObj->ordering		= $row->ordering;
			$blogObj->hits			= $row->hits;
			$blogObj->frontpage     = 1;

			$blog->bind($blogObj);
			$blog->store();

			// Run jomcomment migration here.
			if( $jomcomment )
			{
				$this->migrateJomcomment( $row->id , $blog->id , 'com_myblog' );
			}

			//migrate meta description
			$this->_migrateContentMeta($row->metakey, $row->metadesc, $blog->id);

			//map myblog tags into EasyBlog tags.
			$query  = 'SELECT a.*, b.`name`, b.`slug` FROM `#__myblog_content_categories` as a INNER JOIN `#__myblog_categories` as b';
			$query  .= ' ON a.`category` = b.`id`';
			$query  .= ' WHERE a.`contentid` = ' . $db->Quote($row->id);
			$db->setQuery($query);

			$myblogTags = $db->loadObjectList();

			if(count($myblogTags) > 0)
			{
			    foreach($myblogTags as $item)
			    {
				    $now    = JFactory::getDate();
					$tag	= EasyBlogHelper::getTable( 'Tag', 'Table' );

					if( $tag->exists( $item->name ) )
					{
					    $tag->load( $item->name, true);
					}
					else
					{
					    $tagArr = array();
					    $tagArr['created_by']  	= $this->_getSAUserId();
					    $tagArr['title']  		= $item->name;
					    $tagArr['alias']  		= $item->slug;
					    $tagArr['published']  	= '1';
					    $tagArr['created']     	= $now->toMySQL();

                        $tag->bind($tagArr);
					    $tag->store();
					}

					$postTag	= EasyBlogHelper::getTable( 'PostTag', 'Table' );
					$postTag->tag_id	= $tag->id;
					$postTag->post_id	= $blog->id;
					$postTag->created	= $now->toMySQL();
					$postTag->store();

			    }
			}


			//update session value
			$migrateStat->blog++;
			$statUser   	= $migrateStat->user;
			$statUserObj    = null;
			if(! isset($statUser[$profile->id]))
			{
			    $statUserObj    = new stdClass();
			    $statUserObj->name  		= $profile->nickname;
			    $statUserObj->blogcount		= 0;
			}
			else
			{
			    $statUserObj    = $statUser[$profile->id];
			}
			$statUserObj->blogcount++;
			$statUser[$profile->id] = $statUserObj;
			$migrateStat->user  	= $statUser;


			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');


			//log the entry into migrate table.
			$migrator = EasyBlogHelper::getTable( 'Migrate', 'Table' );

			$migrator->content_id	= $row->id;
			$migrator->post_id		= $blog->id;
			$migrator->session_id	= $jSession->getToken();
			$migrator->component    = 'com_myblog';
			$migrator->store();

			$ejax->append('progress-status4', 'Migrated MyBlog blog post :' . $row->id . ' into EasyBlog with blog id:' . $blog->id . '<br />');
			$ejax->script("ejax.load('migrators','_processMyBlog','$myBlogSection','$jomcomment');");

		}

		$ejax->send();

	}

	function _processLyftenBloggie( $migrateComment )
	{
	    $db			= JFactory::getDBO();
	    $jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->comments	= 0;
			$migrateStat->images	= 0;
			$migrateStat->user      = array();
		}

		$query	= 'SELECT * FROM `#__bloggies_entries` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote('com_lyftenbloggie');
		$query	.= ' )';
		$query	.= ' ORDER BY a.`id` LIMIT 1';

		$db->setQuery($query);
		$row	= $db->loadObject();

		if(is_null($row))
		{
		    // now we migrate the remaining categories
     		$this->_migrateLyftenCategories();

			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status3', '... finished.');
			$ejax->script("divSrolltoBottomLyften();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= 'Total blog posts migrated : ' . $migrateStat->blog . '<br />';
			$stat  .= 'Total comments migrated : ' . $migrateStat->comments . '<br />';
			//$stat  .= 'Total images migrated : ' . $migrateStat->images . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= 'Total user\'s contribution: ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= 'Total blog post from user \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status3', $stat);

			$ejax->script("$( '#migrator-submit3' ).html('Finished. Click here to re-run the process again.');");
			$ejax->script("$( '#migrator-submit3' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait3' ).css( 'display' , 'none' );");

		}
		else
		{
			// here we should process the migration
			// step 1 : create user if not exists in eblog_users - create user through profile jtable load method.
			// step 2: create categories / tags if needed.
			// step 3: migrate comments if needed.

			$date           = JFactory::getDate();
			$blogObj    	= new stdClass();

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			if(! empty($row->catid))
			{

			    $joomlaCat  = $this->_getLyftenCategory($row->catid);

			    $eCat   	= $this->_isEblogCategoryExists($joomlaCat);
				if($eCat === false)
				{
				    $eCat   = $this->_createEblogCategory($joomlaCat);
				}

				$blogObj->category_id   = $eCat;
			}

			$profile	= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$blog		= EasyBlogHelper::getTable( 'Blog', 'Table' );

			//load user profile
			$profile->load( $row->created_by );

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $row->created ) ? $row->created : $date->toMySQL();
			$blogObj->modified		= !empty( $row->modified ) ? $row->modified : $date->toMySQL();

			$blogObj->title			= $row->title;
			$blogObj->permalink		= EasyBlogHelper::getPermalink( $row->title );

			if(empty($row->fulltext))
			{
				$blogObj->intro			= '';
				$blogObj->content		= $row->introtext;
			}
			else
			{
				$blogObj->intro			= $row->introtext;
				$blogObj->content		= $row->fulltext;
			}


			$blogObj->published		= ($row->state == '1') ? '1' : '0'; // set to unpublish for now.
			$blogObj->publish_up 	= !empty( $row->created ) ? $row->created : $date->toMySQL();
			$blogObj->publish_down	= '0000-00-00 00:00:00';

			$blogObj->hits			= $row->hits;
			$blogObj->frontpage     = 1;
			$blogObj->allowcomment  = 1;
			$blogObj->subscription  = 1;

			$blog->bind($blogObj);
			$blog->store();

			//add meta description
			$this->_migrateContentMeta($row->metakey, $row->metadesc, $blog->id);


			//step 2: tags
			$query  = 'insert into `#__easyblog_post_tag` (`tag_id`, `post_id`, `created`)';
			$query  .= ' select a.`id`, ' . $db->Quote($blog->id) . ', ' . $db->Quote($date->toMySQL());
			$query  .= ' from `#__easyblog_tag` as a inner join `#__bloggies_tags` as b';
			$query  .= ' on a.`title` = b.`name`';
			$query  .= ' inner join `#__bloggies_relations` as c on b.`id` = c.`tag`';
			$query  .= ' where c.`entry` = ' . $db->Quote($row->id);

			$db->setQuery($query);
			$db->query();


			// migrate Jcomments from lyftenbloggie into EasyBlog
			// $this->_migrateJCommentIntoEasyBlog($row->id, $blog->id, 'com_lyftenbloggie');
			// step 3
			if($migrateComment)
			{

			    //required frontend model file.
			    require_once (JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'models'.DS.'comment.php');
				$model	= new EasyBlogModelComment();

				$queryComment  = 'SELECT * FROM `#__bloggies_comments` WHERE `entry_id` = ' . $db->Quote($row->id);
				$queryComment  .= ' ORDER BY `id`';
				$db->setQuery($queryComment);
				$resultComment  = $db->loadObjectList();


				if(count($resultComment) > 0)
				{

					$lft    = 1;
					$rgt    = 2;

				    foreach($resultComment as $itemComment)
				    {
	    				$now	= JFactory::getDate();
						$commt	= EasyBlogHelper::getTable( 'Comment', 'Table' );


						$commt->post_id      = $blog->id;
						$commt->comment      = $itemComment->content;
						$commt->title        = '';

						$commt->name         = $itemComment->author;
						$commt->email        = $itemComment->author_email;
						$commt->url          = $itemComment->author_url;
						$commt->created_by   = $itemComment->user_id;
						$commt->created      = $itemComment->date;
						$commt->published    = ($itemComment->state == '1') ? '1' : '0';

						$commt->lft          = $lft;
						$commt->rgt          = $rgt;

						$commt->store();

						//update state
						$migrateStat->comments++;

					    // next set of siblings
					    $lft    = $rgt + 1;
					    $rgt    = $lft + 1;

				    }//end foreach

				}//end if count(comment)

			}


			//update session value
			$migrateStat->blog++;
			$statUser   	= $migrateStat->user;
			$statUserObj    = null;
			if(! isset($statUser[$profile->id]))
			{
			    $statUserObj    = new stdClass();
			    $statUserObj->name  		= $profile->nickname;
			    $statUserObj->blogcount		= 0;
			}
			else
			{
			    $statUserObj    = $statUser[$profile->id];
			}
			$statUserObj->blogcount++;
			$statUser[$profile->id] = $statUserObj;
			$migrateStat->user  	= $statUser;


			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');


			//log the entry into migrate table.
			$migrator = EasyBlogHelper::getTable( 'Migrate', 'Table' );

			$migrator->content_id	= $row->id;
			$migrator->post_id		= $blog->id;
			$migrator->session_id	= $jSession->getToken();
			$migrator->component    = 'com_lyftenbloggie';
			$migrator->store();

			$ejax->append('progress-status3', 'Migrated LyftenBloggie blog post :' . $row->id . ' into EasyBlog with blog id:' . $blog->id . '<br />');
			$ejax->script("ejax.load('migrators','_processLyftenBloggie', '$migrateComment');");

		}//end if else isnull

		$ejax->send();
	}


	function _processSmartBlog($migrateComment, $migrateImage, $imagePath)
	{

		$db			= JFactory::getDBO();
		$jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		//check if com_blog installed.
		if(! JFile::exists(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'blog.php'))
		{
		    $ejax->append('progress-status2', 'Component SmartBlog not found. Action aborted!');
			$ejax->script("$( '#migrator-submit2' ).html('Aborted.');");
			$ejax->script("$( '#migrator-submit2' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait2' ).css( 'display' , 'none' );");
			$ejax->send();
			exit;
		}

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->comments	= 0;
			$migrateStat->images	= 0;
			$migrateStat->user      = array();
		}

		$query	= 'SELECT * FROM `#__blog_postings` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote('com_blog');
		$query	.= ' )';
		$query	.= ' ORDER BY a.`id` LIMIT 1';

		$db->setQuery($query);
		$row	= $db->loadObject();

		if(is_null($row))
		{
			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status2', '... finished.');
			$ejax->script("divSrolltoBottomSmartBlog();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= 'Total blog posts migrated : ' . $migrateStat->blog . '<br />';
			$stat  .= 'Total comments migrated : ' . $migrateStat->comments . '<br />';
			$stat  .= 'Total images migrated : ' . $migrateStat->images . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= 'Total user\'s contribution: ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= 'Total blog post from user \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status2', $stat);

			$ejax->script("$( '#migrator-submit2' ).html('Finished. Click here to re-run the process again.');");
			$ejax->script("$( '#migrator-submit2' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait2' ).css( 'display' , 'none' );");

		}
		else
		{
			// here we should process the migration
			// step 1 : create user if not exists in eblog_users - create user through profile jtable load method.
			// step 2 : migrate image files.
			//      step 2.1: create folder if not exist.
			// step 3: migrate comments if needed.

			$date           = JFactory::getDate();
			$blogObj    	= new stdClass();

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			$profile	= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$blog		= EasyBlogHelper::getTable( 'Blog', 'Table' );

			//load user profile
			$profile->load( $row->user_id );

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $row->post_date ) ? $row->post_date : $date->toMySQL();
			$blogObj->modified		= !empty( $row->post_update ) ? $row->post_update : $date->toMySQL();

			$blogObj->title			= $row->post_title;
			$blogObj->permalink		= EasyBlogHelper::getPermalink( $row->post_title );


			$blogObj->intro			= '';
			$blogObj->content		= $row->post_desc;


			$blogObj->published		= $row->published;
			$blogObj->publish_up 	= !empty( $row->post_date ) ? $row->post_date : $date->toMySQL();
			$blogObj->publish_down	= '0000-00-00 00:00:00';

			$blogObj->hits			= $row->post_hits;
			$blogObj->frontpage     = 1;

			$blog->bind($blogObj);

			//step 2
			$imageMigrated  = false;
			if($migrateImage)
			{
			    $newImagePath   = JPATH_ROOT . DS . 'images';
			    if(! empty($imagePath))
			    {
			        $tmpimagePath	= str_ireplace('/', DS,  $imagePath);
			        $newImagePath   .= DS . $tmpimagePath;
			        $newImagePath   = JFolder::makeSafe($newImagePath);
			    }

			    if(! JFolder::exists($newImagePath))
			    {
			        JFolder::create($newImagePath);
			    }

			    $src	= JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'Images' . DS . 'blogimages' . DS . 'th'.$row->post_image;
			    $dest	= $newImagePath . DS . $row->post_image;


			    if(JFile::exists($src))
			    {
			        $imageMigrated	= JFile::copy($src, $dest);
			    }
			}

			if($imageMigrated)
			{
			    $destSafeURL	= str_ireplace(DS, '/',  $imagePath);
			    $destSafeURL    = 'images/' . $destSafeURL . '/' . $row->post_image;

			    $imageContent	= '<p><img style="padding:0px 10px 10px 0px;" align="left" src="' . $destSafeURL. '" border="0" /> </p>';
			    $blog->content  = $imageContent . $blog->content;
			    $migrateStat->images++;
			}

			$blog->store();

			// step 3
			if($migrateComment)
			{

			    //required frontend model file.
			    require_once (JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'models'.DS.'comment.php');
				$model	= new EasyBlogModelComment();

				$queryComment  = 'SELECT * FROM `#__blog_comment` WHERE `post_id` = ' . $db->Quote($row->id);
				$queryComment  .= ' ORDER BY `id`';
				$db->setQuery($queryComment);
				$resultComment  = $db->loadObjectList();


				if(count($resultComment) > 0)
				{
				    foreach($resultComment as $itemComment)
				    {
						$commentor	= EasyBlogHelper::getTable( 'Profile', 'Table' );

						//load user profile
						$commentor->load( $itemComment->user_id );

						$user   = JFactory::getUser($itemComment->user_id );

	    				$now	= JFactory::getDate();
						$commt	= EasyBlogHelper::getTable( 'Comment', 'Table' );


						$commt->post_id      = $blog->id;
						$commt->comment      = $itemComment->comment_desc;
						$commt->title        = $itemComment->comment_title;

						$commt->name         = $user->name;
						$commt->email        = $user->email;
						$commt->url          = $commentor->url;
						$commt->created_by   = $itemComment->user_id;
						$commt->created      = $itemComment->comment_date;
						$commt->published    = $itemComment->published;


						//adding new comment
						$latestCmmt	= $model->getLatestComment($blog->id, '0');
						$lft	= 1;
						$rgt	= 2;

						if(! empty($latestCmmt))
						{
						 	$lft	= $latestCmmt->rgt + 1;
						 	$rgt	= $latestCmmt->rgt + 2;

						 	$model->updateCommentSibling($blog->id, $latestCmmt->rgt);
						}

						$commt->lft          = $lft;
						$commt->rgt          = $rgt;

						$commt->store();

						//update state
						$migrateStat->comments++;

				    }//end foreach

				}//end if count(comment)

			}



			//update session value
			$migrateStat->blog++;
			$statUser   	= $migrateStat->user;
			$statUserObj    = null;
			if(! isset($statUser[$profile->id]))
			{
			    $statUserObj    = new stdClass();
			    $statUserObj->name  		= $profile->nickname;
			    $statUserObj->blogcount		= 0;
			}
			else
			{
			    $statUserObj    = $statUser[$profile->id];
			}
			$statUserObj->blogcount++;
			$statUser[$profile->id] = $statUserObj;
			$migrateStat->user  	= $statUser;


			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');


			//log the entry into migrate table.
			$migrator = EasyBlogHelper::getTable( 'Migrate', 'Table' );

			$migrator->content_id	= $row->id;
			$migrator->post_id		= $blog->id;
			$migrator->session_id	= $jSession->getToken();
			$migrator->component    = 'com_blog';
			$migrator->store();

			$ejax->append('progress-status2', 'Migrated SmartBlog blog post :' . $row->id . ' into EasyBlog with blog id:' . $blog->id . '<br />');
			$ejax->script("ejax.load('migrators','_processSmartBlog','$migrateComment', '$migrateImage', '$imagePath');");

		}

		$ejax->send();

	}

	function _process($authorId, $stateId, $catId, $sectionId, $myblogSection , $jomcomment = false )
	{
		$db			= JFactory::getDBO();
		$jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

		$query	= 'SELECT * FROM `#__content` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote('com_content');
		$query	.= ' )';
		if($authorId != '0')
			$query	.= ' AND a.`created_by` = ' . $db->Quote($authorId);

		if($stateId != '*')
		{
			switch($stateId)
			{
				case 'P':
					$query	.= ' AND a.`state` = ' . $db->Quote('1');
					break;
				case 'U':
					$query	.= ' AND a.`state` = ' . $db->Quote('0');
					break;
				case 'A':
					$query	.= ' AND a.`state` = ' . $db->Quote('-1');
					break;

				// joomla 1.6 compatibility
				case '1': // publish
					$query	.= ' AND a.`state` = ' . $db->Quote('1');
					break;
				case '0': //unpublish
					$query	.= ' AND a.`state` = ' . $db->Quote('0');
					break;
				case '2': // archive
					$query	.= ' AND a.`state` = ' . $db->Quote('2');
					break;
				case '-2': // trash
					$query	.= ' AND a.`state` = ' . $db->Quote('-2');
					break;

				default:
					break;
			}
		}
		if($sectionId != '-1')
			$query	.= ' AND a.`sectionid` = ' . $db->Quote($sectionId);

		// we do not want the myblog post process here.
		if($myblogSection != '')
			$query	.= ' AND a.`sectionid` != ' . $db->Quote($myblogSection);

		if($catId != '0')
			$query	.= ' AND a.`catid` = ' . $db->Quote($catId);

		$query	.= ' ORDER BY a.`id` LIMIT 1';

		$db->setQuery($query);
		$row	= $db->loadObject();

		if(is_null($row))
		{
			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status', '... finished.');
			$ejax->script("divSrolltoBottom();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= 'Total joomla article migrated : ' . $migrateStat->blog . '<br />';
			$stat  .= 'Total joomla category migrated : ' . $migrateStat->category . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= 'Total user\'s contribution: ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= 'Total articles from user \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status', $stat);

			$ejax->script("$( '#migrator-submit' ).html('Finished. Click here to re-run the process again.');");
			$ejax->script("$( '#migrator-submit' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait' ).css( 'display' , 'none' );");
		}
		else
		{
			// here we should process the migration

			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.

			$date           = JFactory::getDate();
			$blogObj    	= new stdClass();

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			if(! empty($row->catid))
			{

			    $joomlaCat  = $this->_getJoomlaCategory($row->catid);

			    $eCat   	= $this->_isEblogCategoryExists($joomlaCat);
				if($eCat === false)
				{
				    $eCat   = $this->_createEblogCategory($joomlaCat);
				}

				$blogObj->category_id   = $eCat;
			}

			$profile	= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$blog		= EasyBlogHelper::getTable( 'Blog', 'Table' );

			//load user profile
			$profile->load( $row->created_by );

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $row->created ) ? $row->created : $date->toMySQL();
			$blogObj->modified		= $date->toMySQL();

			$blogObj->title			= $row->title;
			$blogObj->permalink		= $row->alias;

			// Need to remap the access.
			$access					= 0;

            if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
            {
				switch($row->access)
				{
				    case 1:
				        $access = 0;
				        break;
				    default:
				        $access = 1;
				        break;
				}
			}
			else
			{
			   	$access = ($row->access == 2) ? 1 : $row->access;
			}

			$blogObj->private		= $access;
			if(empty($row->fulltext))
			{
				$blogObj->intro			= '';
				$blogObj->content		= $row->introtext;
			}
			else
			{
				$blogObj->intro			= $row->introtext;
				$blogObj->content		= $row->fulltext;
			}

			//translating the article state into easyblog publish status.
			$blogState  = '';
			if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
			{
			    $blogState  = ($row->state == 2 || $row->state == -2) ? 0 : $row->state;
			}
			else
			{
			    $blogState  = ($row->state == -1) ? 0 : $row->state;
			}

			$blogObj->published		= $blogState;
			$blogObj->publish_up 	= !empty( $row->publish_up )? $row->publish_up : $date->toMySQL();
			$blogObj->publish_down	= !empty( $row->publish_down )? $row->publish_down : $date->toMySQL();

			$blogObj->ordering		= $row->ordering;
			$blogObj->hits			= $row->hits;
			$blogObj->frontpage     = 1;

			$blog->bind($blogObj);
			$blog->store();

			// Run jomcomment migration here.
			if( $jomcomment )
			{
				$this->migrateJomcomment( $row->id , $blog->id , 'com_content' );
			}

			//migrate meta description
			$this->_migrateContentMeta($row->metakey, $row->metadesc, $blog->id);

			//isfeatured! only applicable in joomla1.6
			if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
			{
			    if($row->featured)
			    {
			        EasyBlogHelper::makeFeatured('post', $blog->id);
			    }
			}

			//update session value
			$migrateStat->blog++;
			$statUser   	= $migrateStat->user;
			$statUserObj    = null;
			if(! isset($statUser[$profile->id]))
			{
			    $statUserObj    = new stdClass();
			    $statUserObj->name  		= $profile->nickname;
			    $statUserObj->blogcount		= 0;
			}
			else
			{
			    $statUserObj    = $statUser[$profile->id];
			}
			$statUserObj->blogcount++;
			$statUser[$profile->id] = $statUserObj;
			$migrateStat->user  	= $statUser;


			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');


			//log the entry into migrate table.
			$migrator = EasyBlogHelper::getTable( 'Migrate', 'Table' );

			$migrator->content_id	= $row->id;
			$migrator->post_id		= $blog->id;
			$migrator->session_id	= $jSession->getToken();
			$migrator->component    = 'com_content';
			$migrator->store();


			$ejax->append('progress-status', 'Migrated joomla article :' . $row->id . ' into EasyBlog with blog id:' . $blog->id . '<br />');
			$ejax->script("ejax.load('migrators','_process','$authorId', '$stateId', '$catId', '$sectionId', '$myblogSection','$jomcomment');");
		}
		$ejax->send();
	}

	function _getJoomlaCategory( $catId )
	{
	    $db = JFactory::getDBO();

	    $query  = 'select * from `#__categories` where `id` = ' . $db->Quote($catId);
	    $db->setQuery($query);
	    $result = $db->loadObject();

	    return $result;
	}

	public function _getK2Category( $catId )
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT * FROM `#__k2_categories` where `id` = ' . $db->Quote($catId);
		$db->setQuery($query);
		$result = $db->loadObject();

		// Mimic Joomla's category behavior
		if( $result )
		{
			$result->title	= $result->name;
		}
		return $result;
	}

	function _isEblogCategoryExists( $joomlaCatObj )
	{
	    $db = JFactory::getDBO();

	    $query  = 'select id from `#__easyblog_category`';
		$query	.= ' where lower(`title`) = ' . $db->Quote(JString::strtolower($joomlaCatObj->title));
		$query  .= ' OR lower(`alias`) = ' . $db->Quote(JString::strtolower($joomlaCatObj->alias));
		$query  .= ' LIMIT 1';

	    $db->setQuery($query);
	    $result = $db->loadResult();

	    if(empty($result))
	        return false;
	    else
	        return $result;
	}

	function _createEblogCategory($joomlaCatObj)
	{
		$jSession 		= JFactory::getSession();
		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

	    $category	= EasyBlogHelper::getTable( 'ECategory', 'Table' );

	    $arr    = array();
	    $arr['created_by']  = $this->_getSAUserId();
	    $arr['title']  		= $joomlaCatObj->title;
	    $arr['alias']  		= $joomlaCatObj->alias;
	    $arr['published']  	= ( isset($joomlaCatObj->published) ) ? $joomlaCatObj->published : 1;

	    $category->bind($arr);
	    $category->store();

	    //update session value
	    $migrateStat->category++;
	    $jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');

	    return $category->id;
	}

	function _getSAUserId()
	{
		$saUserId   = '62';
		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$saUsers	= EasyBlogHelper::getSAUsersIds();

			$saUserId = '42';
			if(count($saUsers) > 0)
			{
			    $saUserId = $saUsers['0'];
			}
		}
		return $saUserId;
	}

	function _getLyftenCategory($catId)
	{
	    $db = JFactory::getDBO();

	    $query  = 'select *, slug as `alias` from `#__bloggies_categories` where `id` = ' . $db->Quote($catId);
	    $db->setQuery($query);

	    $result = $db->loadObject();
	    $result->alias  = JFilterOutput::stringURLSafe( trim( $result->slug ) );

	    return $result;
	}

	function _migrateContentMeta($metaKey, $metaDesc, $blogId)
	{
	    $db 	= JFactory::getDBO();

	    if(empty($metaKey) && empty($metaDesc))
	    {
			return true;
	    }

	    $meta				= EasyBlogHelper::getTable( 'Meta', 'Table' );
	    $meta->keywords		= $metaKey;
	    $meta->description	= $metaDesc;
	    $meta->content_id	= $blogId;
	    $meta->type			= 'post';
		$meta->store();

		return true;
	}

	function _migrateLyftenTags()
	{
	    //this will plot all lyften bloggie tags into easyblog's tags
	    // no relations created for each blog vs tag

	    $db 	= JFactory::getDBO();
	    $suId   = $this->_getSAUserId();
	    $now	= JFactory::getDate();

	    $query  = 'insert into `#__easyblog_tag` (`created_by`, `title`, `alias`, `created`, `published`)';
		$query  .= ' select ' . $db->Quote($suId) . ', `name`, `slug`, '. $db->Quote($now->toMySQL()).', ' . $db->Quote('1');
		$query  .= ' from `#__bloggies_tags`';
		$query  .= ' where `name` not in (select `title` from `#__easyblog_tag`)';

		$db->setQuery($query);
		$db->query();

		return true;
	}

	function _migrateLyftenCategories()
	{
		$jSession 		= JFactory::getSession();
		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

	    $db 	= JFactory::getDBO();
	    $suId   = $this->_getSAUserId();
	    $now	= JFactory::getDate();

		$query  = ' select `title`, `slug`, `published`';
		$query  .= ' from `#__bloggies_categories`';
		$query  .= ' where `title` != \'\' and `title` not in (select `title` from `#__easyblog_category`)';

		$db->setQuery($query);
		$results    = $db->loadObjectList();

		$suId       = $this->_getSAUserId();

		for($i = 0; $i < count($results); $i++)
		{
		    $catObj     = $results[$i];

		    $category	= EasyBlogHelper::getTable( 'ECategory', 'Table' );

		    $arr    = array();
		    $arr['created_by']  = $suId;
		    $arr['title']  		= $catObj->title;
		    $arr['alias']  		= JFilterOutput::stringURLSafe(trim($catObj->slug));
		    $arr['published']  	= $catObj->published;

		    $category->bind($arr);
		    $category->store();

		    //update session value
		    $migrateStat->category++;

		}

		if(count($results) > 0)
		{
			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');
		}

		return true;
	}

	public function migrateK2Comments( $k2obj , $blog )
	{
		$db			= JFactory::getDBO();
		$jSession 	= JFactory::getSession();

		$query	= 'SELECT * FROM `#__k2_comments` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote('com_k2.comments');
		$query	.= ' ) ';
		$query	.= 'AND a.' . $db->nameQuote( 'itemID' ) . ' = ' . $db->Quote( $k2obj->id ) . ' ORDER BY a.`id` ASC';

		$db->setQuery( $query );

		$comments	= $db->loadObjectList();

		if( !$comments )
		{
			return;
		}

		$lft		= 1;
		$rgt		= 2;

		foreach( $comments as $comment )
		{
	        $post				= array();
	        $post['id'] 		= $blog->id;
	        $post['comment']    = $comment->commentText;
			$post['name']       = $comment->userName;

			// @rule: Since K2 does not store any title for comments, we just leave this blank.
			$post['title']      = '';

			$post['email']      = $comment->commentEmail;
			$post['url']        = $comment->commentURL;

            $table		= JTable::getInstance( 'Comment' , 'Table' );
            $table->bindPost($post);

            //the rest info assign here.
            $table->lft   		= $lft;
			$table->rgt			= $rgt;

			$table->created_by 	= $comment->userID;
			$table->created    	= $comment->commentDate;
			$table->modified	= $comment->commentDate;
			$table->published  	= $comment->published;

            $table->store();

			//log the entry into migrate table.
			$migrator = EasyBlogHelper::getTable( 'Migrate', 'Table' );

			$migrator->content_id	= $comment->id;
			$migrator->post_id		= $table->id;
			$migrator->session_id	= $jSession->getToken();
			$migrator->component    = 'com_k2.comments';
			$migrator->store();

	        //do not touch this settings!
	        $lft    = $rgt + 1;
	        $rgt    = $lft + 1;
		}
	}

	public function migrateJomcomment( $contentId , $blogId , $option )
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__jomcomment' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'contentid' ) . ' = ' . $db->Quote( $contentId ) . ' '
				. 'AND ' . $db->nameQuote( 'option' ) . ' = ' . $db->Quote( $option ) . ' '
				. 'ORDER BY `id` ASC';

		$db->setQuery( $query );
		$comments	= $db->loadObjectList();


		if( !$comments )
		{
			return;
		}


		$lft		= 1;
		$rgt		= 2;

		foreach( $comments as $comment )
		{
	        $post				= array();

	        $post['id'] 		= $blogId;
	        $post['comment']    = $comment->comment;
			$post['name']       = $comment->name;
			$post['title']      = $comment->title;
			$post['email']      = $comment->email;
			$post['url']        = $comment->website;

            $table		= JTable::getInstance( 'Comment' , 'Table' );
            $table->bindPost($post);

            //the rest info assign here.
            $table->lft   		= $lft;
            $table->rgt			= $rgt;
            $table->ip   		= $comment->ip;
            $table->created_by 	= $comment->user_id;
            $table->created    	= $comment->date;
            $table->modified	= $comment->date;
            $table->published  	= $comment->published;
            $table->ordering   	= $comment->ordering;
            $table->vote       	= $comment->voted;

            $table->store();

	        //do not touch this settings!
	        $lft    = $rgt + 1;
	        $rgt    = $lft + 1;
		}
	}

	function _migrateJCommentIntoEasyBlog($contentId, $blogId, $contentGroup)
	{
        $db 	= JFactory::getDBO();

        $query  = 'select * from `#__jcomments`';
		$query	.= ' where `object_id` = ' . $db->Quote($contentId);
		$query  .= ' and `object_group` = ' . $db->Quote($contentGroup);
		$query  .= ' order by `id` asc';

		$db->setQuery($query);

		$results    = $db->loadObjectList();

		$lft    = 1;
		$rgt    = 2;

		for($i = 0; $i < count($results); $i++)
		{
		    $itemComment   = $results[$i];

			$commt		= EasyBlogHelper::getTable( 'Comment', 'Table' );
			$now		= JFactory::getDate();

			$commt->post_id      = $blogId;
			$commt->comment      = $itemComment->comment;
			$commt->title        = $itemComment->title;

			$commt->name         = $itemComment->name;
			$commt->email        = $itemComment->email;
			$commt->url          = $itemComment->homepage;
			$commt->created_by   = $itemComment->userid;
			$commt->created      = $itemComment->date;
			$commt->published    = $itemComment->published;

			$commt->lft          = $lft;
			$commt->rgt          = $rgt;

			$commt->store();

			//update state
			$migrateStat->comments++;

		    // next set of siblings
		    $lft    = $rgt + 1;
		    $rgt    = $lft + 1;
		}

		return true;
	}


}
