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

/** Import library dependencies */
jimport('joomla.plugin.plugin');

class plgUserJ2Store extends JPlugin
{
    function plgUserJ2Store(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage( '', JPATH_ADMINISTRATOR );
    }

    /**
     * When the user logs in, their session cart should override their db-stored cart.
     * Current actions take precedence
     *
     * @param $user
     * @param $options
     * @return unknown_type
     */
    function onUserLogin($user, $options)
    {
    	$session =& JFactory::getSession();
    	$old_sessionid = $session->get( 'old_sessionid' );

    	$user['id'] = intval(JUserHelper::getUserId($user['username']));
    	
    	// Should check that K2 Store is installed first before executing
        if (!$this->_isInstalled())
        {
            return;
        }
        
        JLoader::register( "J2StoreHelperCart", JPATH_SITE.DS."components".DS."com_j2store".DS."helpers".DS."cart.php" );

        $helper = new J2StoreHelperCart();
        if (!empty($old_sessionid))
        {
            $helper->mergeSessionCartWithUserCart( $old_sessionid, $user['id'] );
        }
            else
        {
            $helper->updateUserCartItemsSessionId( $user['id'], $session->getId() );
        }
       
       return true;
    }

    /**
     * Checks the extension is installed 
     *
     * @return boolean
     */
    function _isInstalled()
    {
        $success = false;

        jimport('joomla.filesystem.file');
        if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'admin.j2store.php'))
        {
            $success = true;
        }
        return $success;
    }
   
   
}
