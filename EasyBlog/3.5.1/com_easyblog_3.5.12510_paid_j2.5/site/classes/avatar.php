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

class EasyBlogAvatarDefault 
{
	function _init()
	{
		return true;
	}
	
	function _getAvatar($profile)
	{
		$path  			= JPATH_ROOT . DS . EasyImageHelper::getAvatarRelativePath() . DS . $profile->avatar;
		$image			= EasyImageHelper::getAvatarRelativePath() . '/' . $profile->avatar;
		$mainframe		= JFactory::getApplication();
		$override		= JPATH_ROOT . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . 'com_easyblog' . DS . 'assets' . DS . 'images' . DS . 'default_blogger.png';

		if( !JFile::exists( $path ) || empty( $profile->avatar ) )
		{
			if( JFile::exists( $override ) )
			{
				$image	= 'templates/' . $mainframe->getTemplate() . '/html/com_easyblog/assets/images/default_blogger.png';
			}
			else
			{
				$image	= 'components/com_easyblog/assets/images/default_blogger.png';
			}
		}
		
		$avatar 		= new stdClass();
		$avatar->link	= rtrim( JURI::root() , '/' ) . '/' . $image;

		return $avatar;
	}
}

class EasyBlogAvatarJomsocial
{
	function _init()
	{
		$files	= JPATH_ROOT . DS . 'components' . DS. 'com_community' . DS . 'libraries' . DS . 'core.php';
		
		if(!JFile::exists( $files ))
		{
			return false;
		}
		
		require_once( $files );
			
		return true;		
	}
	
	function _getAvatar($profile)
	{
		$user   = null;
		if( is_null( $profile->user) )
		{
			$user	= CFactory::getUser(0);
		}
		else
		{
			$user	= CFactory::getUser( $profile->id );
		}
		
		$source	= $user->getThumbAvatar();

		$avatar = new stdClass();
		$avatar->link	= $source;
		
		return $avatar;
	}
}

class EasyBlogAvatarJomWall
{
	function _init()
	{
		$files	= JPATH_ROOT . DS . 'components' . DS. 'com_awdwall' . DS . 'helpers' . DS . 'user.php';
		
		if(!JFile::exists( $files ))
		{
			return false;
		}
		
		require_once( $files );
			
		return true;		
	}
	
	function _getAvatar($profile)
	{
		$avatar = new stdClass();
		$avatar->link	= AwdwallHelperUser::getBigAvatar51($profile->id);;
		
		return $avatar;
	}

	public function _getLink()
	{
		$Itemid = AwdwallHelperUser::getComItemId();	
		$link = EasyBlogRouter::_('index.php?option=com_awdwall&view=awdwall&layout=mywall&wuid='.$profile->id.'&Itemid='.$Itemid, false);
		return $link;
	}
}

class EasyBlogAvatarK2
{
	function _init()
	{
		$files	= JPATH_ROOT . DS . 'components' . DS. 'com_k2' . DS . 'k2.php';
		
		if(!JFile::exists( $files ))
		{
			return false;
		}

		return true;		
	}
	
	function _getAvatar($profile)
	{
		$db 	= JFactory::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__k2_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'userID' ) . '=' . $db->Quote( $profile->id );

		$db->setQuery( $query );
		$result	= $db->loadObject();

		if( !$profile->id || !$result )
		{
			return EasyBlogAvatarDefault::_getAvatar( $profile );
		}


		$avatar	= new stdClass();
		$avatar->link	= rtrim( JURI::root() , '/' ) . '/media/k2/users/' . $result->image;

		return $avatar;
	}
}

class EasyBlogAvatarMightyTouch
{
	function _init()
	{
		$file	= JPATH_ROOT . DS . 'components' . DS . 'com_juser' . DS . 'api.php';
		
		if( !JFile::exists( $file ) )
		{
			return false;
		}
		
		require_once( $file );
		
		return true;
	}
	
	public function _getAvatar( $profile )
	{
		$avatar = new stdClass();
		$avatar->link	= JSUserApi::getAvatarPath( $profile->id );
		
		return $avatar;
	}
}

class EasyBlogAvatarKunena
{

	function _init()
	{
		$files	= JPATH_ROOT . DS . 'components' . DS. 'com_kunena' . DS . 'class.kunena.php';		
		
		if(!JFile::exists( $files ))
		{
			return false;
		}
		
		return true;		
	}
	
	function _getAvatar($profile)
	{
		$db	= JFactory::getDBO();
		
		//$sql = 'SELECT a.*, b.* FROM #__fb_users AS a INNER JOIN #__users AS b ON b.id=a.userid WHERE a.userid='.$db->quote($profile->id);	
		$sql = 'SELECT a.*, b.* FROM #__kunena_users AS a INNER JOIN #__users AS b ON b.id=a.userid WHERE a.userid='.$db->quote($profile->id);		
		$db->setQuery($sql);

    	$user 	= $db->loadObject();
    	//$path	= 'images/fbfiles/avatars';
    	$path	= 'media/kunena/avatars';
    	$source	= empty($user->avatar)? 'nophoto.jpg' : str_replace( '{', '', $user->avatar);

		
		$avatar = new stdClass();
		$avatar->link	= JURI::root() . $path . '/' . $source;
    	return $avatar;    	
	}
}

class EasyBlogAvatarCommunityBuilder
{
	function _init()
	{
		$files = JPATH_ROOT . DS . 'administrator' . DS . 'components' .DS. 'com_comprofiler' .DS. 'plugin.foundation.php';
        
		if(!JFile::exists( $files ))
		{
			return false;
		}
		
		require_once( $files );
		cbimport('cb.database');
		cbimport('cb.tables');
		cbimport('cb.tabs');
		
		return true;
	}

	function _getAvatar($profile)
	{
		$user = CBuser::getInstance( $profile->id );

		// @task: Apply guest avatars when necessary.
		if( !$profile->id )
		{
			$avatar 		= new stdClass();
			$avatar->link 	= selectTemplate() . 'images/avatar/tnnophoto_n.png'; 

			return $avatar;
		}

		if (!$user) 
		{
			$user = CBuser::getInstance( null );
		}
		
		$source	=	$user->avatarFilePath();
		
		//incase we view from backend. we need to remove the /administrator/ from the path.
		$source = str_replace('/administrator/','/', $source);
		
		$avatar = new stdClass();
		$avatar->link	= $source;
		
		return $avatar;
	}
}
	
class EasyBlogAvatarGravatar
{	
	function _init()
	{
        return true;
	}

	function _getAvatar($profile)
	{
		$user = JFactory::getUser($profile->id);
		
		$avatar = new stdClass();	
		$avatar->link	= 'https://secure.gravatar.com/avatar.php?gravatar_id=' . md5($user->email)
	            		//. '&amp;default=' . urlencode(JURI::root().$profile->avatar)
						. '&amp;size=60';
		
		return $avatar;
	}
}
	
class EasyBlogAvatarPhpBB
{
	var $files;
	var $phpbbpath;
	var $phpbbuserid;
	
	function _init()
	{
		$config = EasyBlogHelper::getConfig();		
		$this->phpbbpath = $config->get( 'layout_phpbb_path' );
		
		$this->files = JPATH_ROOT . DS . $this->phpbbpath . DS . 'config.php';    
	    
	    if(!JFile::exists( $this->files ))
		{
			return false;
		}
		
		return true;		
	}
	
	function _getAvatar($profile)
	{
		$phpbbDB = $this->_getPhpbbDBO();
		$phpbbConfig = $this->_getPhpbbConfig();
		
		if(empty($phpbbConfig))
		{
			return false;
		}
		
		$juser	= JFactory::getUser( $profile->id );
			
		$sql	= 'SELECT '.$phpbbDB->nameQuote('user_id').', '.$phpbbDB->nameQuote('username').', '.$phpbbDB->nameQuote('user_avatar').', '.$phpbbDB->nameQuote('user_avatar_width').', '.$phpbbDB->nameQuote('user_avatar_height').', '.$phpbbDB->nameQuote('user_avatar_type').' '
				. 'FROM '.$phpbbDB->nameQuote('#__users').' WHERE LOWER('.$phpbbDB->nameQuote('username').') = '.$phpbbDB->quote( strtolower( $juser->username) ).' '
				. 'LIMIT 1';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObject();
		
		$this->phpbbuserid = empty($result->user_id)? '0' : $result->user_id;
		
		if(!empty($result->user_avatar))
		{
			//avatar upload		1
			//avatar remote		2
			//avatar gallery	3		
			switch($result->user_avatar_type)
			{
				case '1':
					$subpath	= $phpbbConfig->avatar_upload_path;
					$phpEx 		= JFile::getExt(__FILE__);				
					$source		= JURI::root().$this->phpbbpath.'/download/file.'.$phpEx.'?avatar='.$result->user_avatar;
					break;
				case '2':
					$source		= $result->user_avatar;
					break;
				case '3':
					$subpath	= $phpbbConfig->avatar_gallery_path;
					$source		= JURI::root().$this->phpbbpath.'/'.$subpath.'/'.$result->user_avatar;
					break;
				default:
					$subpath = '';
			}
		}
		else
		{
			$sql	= 'SELECT '.$phpbbDB->nameQuote('theme_name').' '
					. 'FROM '.$phpbbDB->nameQuote('#__styles_theme').' ' 
					. 'WHERE '.$phpbbDB->nameQuote('theme_id').' = '.$phpbbDB->quote($phpbbConfig->default_style);
			$phpbbDB->setQuery($sql);
			$theme = $phpbbDB->loadObject();
			
			$defaultPath	= $this->phpbbpath.'/styles/'.$theme->theme_name.'/theme/images/no_avatar.gif';
			$source			= JURI::root().$defaultPath;
		}
		
		$avatar = new stdClass();
		$avatar->link	= $source;
		
		return $avatar;		
	}
	
	function _getPhpbbDBO()
	{
		static $phpbbDB = null;
        
		if($phpbbDB == null)
		{
			require( $this->files );
		
			$host		= $dbhost;
			$user		= $dbuser;
			$password	= $dbpasswd;
			$database	= $dbname;
			$prefix		= $table_prefix;
			$driver		= $dbms;
			$debug		= 0;
			
			$options = array ( 'driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );
			
			$phpbbDB = JDatabase::getInstance( $options );
		}
		
		return $phpbbDB;
	}
	
	function _getPhpbbConfig()
	{
		$phpbbDB = $this->_getPhpbbDBO();
		
		$sql	= 'SELECT '.$phpbbDB->nameQuote('config_name').', '.$phpbbDB->nameQuote('config_value').' '
				. 'FROM '.$phpbbDB->nameQuote('#__config') . ' '
				. 'WHERE '.$phpbbDB->nameQuote('config_name').' IN ('.$phpbbDB->quote('avatar_gallery_path').', '.$phpbbDB->quote('avatar_path').', '.$phpbbDB->quote('default_style').')';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObjectList();
		
		if(empty($result))
		{
			return false;
		}
				
		$phpbbConfig = new stdClass();
        $phpbbConfig->avatar_gallery_path	= null;
        $phpbbConfig->avatar_upload_path	= null;
		$phpbbConfig->default_style			= 1;
				
		foreach($result as $row)
		{
			switch($row->config_name)
			{
				case 'avatar_gallery_path':
					$phpbbConfig->avatar_gallery_path = $row->config_value;
					break;
				case 'avatar_path':
					$phpbbConfig->avatar_upload_path = $row->config_value;
					break;
				case 'default_style':
					$phpbbConfig->default_style = $row->config_value;
					break;
			}
		}
		
		return $phpbbConfig;
	}
}

class EasyBlogAvatarAnahita
{
	function _init()
	{
		if( !class_exists( 'KFactory' ) )
		{
			return false;	
		}

		return true;
	}
	
	function _getAvatar( $profile )
	{

		$person	= KFactory::get( 'lib.anahita.se.person.helper' )->getPerson( $profile->id );
		
		$avatar			= new stdClass();
		$avatar->link	= $person->getAvatar()->getURL( AnSeAvatar::SIZE_MEDIUM );
		
		return $avatar;
	}
}
	
class EasyBlogAvatarTuiyo
{
	function _init()
	{
		return false;		
	}
	
	function _getAvatar()
	{
	
	}
}