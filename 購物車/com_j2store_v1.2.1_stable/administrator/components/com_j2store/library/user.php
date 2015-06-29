<?php
/*------------------------------------------------------------------------
# com_j2store - J2 Store v 1.0
# ------------------------------------------------------------------------
# author    Sasi varna kumar - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2012 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://j2store.org
# Technical Support:  Forum - http://j2store.org/forum/index.html
-------------------------------------------------------------------------*/


/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class J2StoreHelperUser
{

	/**
	 *
	 * @param $string
	 * @return unknown_type
	 */
	function usernameExists( $string )
	{
		// TODO Make this use ->load()

		$success = false;
		$database = JFactory::getDBO();
		$string = $database->getEscaped($string);
		$query = "
			SELECT
				*
			FROM
				#__users
			WHERE 1
			AND
				`username` = '{$string}'
			LIMIT 1
		";
		$database->setQuery($query);
		$result = $database->loadObject();
		if ($result) {
			$success = true;
		}
		return $success;
	}

	/**
	 *
	 * @param $string
	 * @return unknown_type
	 */
	function emailExists( $string, $table='users'  ) {
		switch($table)
		{
			case 'accounts' :
				$table = '#__tienda_accounts';
				break;

			case  'users':
			default     :
				$table = '#__users';
		}

		$success = false;
		$database = JFactory::getDBO();
		$string = $database->getEscaped($string);
		$query = "
			SELECT
				*
			FROM
				$table
			WHERE 1
			AND
				`email` = '{$string}'
			LIMIT 1
		";
		$database->setQuery($query);
		$result = $database->loadObject();
		if ($result) {
			$success = true;
		}
		return $result;
	}


	/**
	 * Returns yes/no
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function createNewUser( $details, &$msg )
	{
		$instance = JUser::getInstance();

		jimport('joomla.application.component.helper');
		$config = JComponentHelper::getParams('com_users');
		// Default to Registered.
		$defaultUserGroup = $config->get('new_usertype', 2);
		$md5_pass = md5($details['password']);

		$acl = JFactory::getACL();

		$instance->set('id'         , 0);
		$instance->set('name'           , $details['name']);
		$instance->set('username'       , $details['email']);
		$instance->set('password' 		, $md5_pass );
		$instance->set('email'          , $details['email']);  // Result should contain an email (check)
		$instance->set('usertype'       , 'deprecated');
		$instance->set('groups'     , array($defaultUserGroup));

		//If autoregister is set let's register the user
		$autoregister = isset($options['autoregister']) ? $options['autoregister'] :  $config->get('autoregister', 1);

		if ($autoregister) {
		    if (!$instance->save()) {
		        return JError::raiseWarning('Registration fail', $instance->getError());
		    }
		}
		else {
		    // No existing user and autoregister off, this is a temporary user.
		    $instance->set('tmp_user', true);
		}

		$useractivation='0';

		// Send registration confirmation mail
		J2StoreHelperUser::_sendMail( $instance, $details, $useractivation );

		return $instance;
	}

	/**
	 * Returns yes/no
	 * @param array [username] & [password]
	 * @param mixed Boolean
	 *
	 * @return array
	 */
	function login( $credentials, $remember=true, $return='' ) {

		$mainframe  = &JFactory::getApplication();

		if (strpos( $return, 'http' ) !== false && strpos( $return, JURI::base() ) !== 0) {
			$return = '';
		}

		// $credentials = array();
		// $credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
		// $credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);

		$options = array();
		$options['remember'] = (boolean)$remember;
		//$options['return'] = $return;

		//preform the login action
		$success = $mainframe->login($credentials);

		if ( $return ) {
			$mainframe->redirect( $return );
		}

		return $success;
	}

	/**
	 * Returns yes/no
	 * @param mixed Boolean
	 * @return array
	 */
	function logout( $return='' ) {
		$mainframe  = &JFactory::getApplication();

		//preform the logout action//check to see if user has a joomla account
		//if so register with joomla userid
		//else create joomla account
		$success = $mainframe->logout();

		if (strpos( $return, 'http' ) !== false && strpos( $return, JURI::base() ) !== 0) {
			$return = '';
		}

		if ( $return ) {
			$mainframe->redirect( $return );
		}

		return $success;
	}

    /**
     * Unblocks a user
     *
     * @param int $user_id
     * @param int $unblock
     * @return boolean
     */
    function unblockUser($user_id, $unblock = 1)
    {
        $user =& JFactory::getUser( (int)$user_id );

        if ($user->get('id')) {
            $user->set('block', !$unblock);

            if (  ! $user->save()) {
                return false;
            }

            return true;
        }
        else {
            return false;
        }
    }

	/**
	 * Returns yes/no
	 * @param object
	 * @param mixed Boolean
	 * @return array
	 */
	function _sendMail( &$user, $details, $useractivation ) {
		$mainframe  = &JFactory::getApplication();

		$db		=& JFactory::getDBO();

		$name 		= $user->get('name');
		if(empty($name)) {
			$name 		= $user->get('email');
		}
		$email 		= $user->get('email');
		$username 	= $user->get('username');
		$activation	= $user->get('activation');
		$password 	= $details['password2']; // using the original generated pword for the email

		$usersConfig 	= &JComponentHelper::getParams( 'com_users' );
		// $useractivation = $usersConfig->get( 'useractivation' );
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= JURI::base();

		$subject 	= JText::sprintf('J2STORE_ACCOUNT_DETAILS', $name, $sitename);
		$subject 	= html_entity_decode($subject, ENT_QUOTES);

		if ( $useractivation == 1 ){
			$message = JText::sprintf( 'J2STORE_SEND_MSG_ACTIVATE', $name, $sitename, $siteURL."index.php?option=com_user&task=activate&activation=".$activation, $siteURL, $email, $password);
		} else {
			$message = JText::sprintf('J2STORE_SEND_MSG', $name, $sitename, $siteURL, $email, $password );
		}

		$message = html_entity_decode($message, ENT_QUOTES);

		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
				' FROM #__users' .
				' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// Send email to user
		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}

		$success = J2StoreHelperUser::_doMail($mailfrom, $fromname, $email, $subject, $message);

		return $success;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function _doMail( $from, $fromname, $recipient, $subject, $body, $actions=NULL, $mode=NULL, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL )
	{
		$success = false;

		$message =& JFactory::getMailer();
		$message->addRecipient( $recipient );
		$message->setSubject( $subject );
		$message->setBody( $body );
		$sender = array( $from, $fromname );
		$message->setSender($sender);
		$sent = $message->send();
		if ($sent == '1') {
			$success = true;
		}

		return $success;
	}
}
