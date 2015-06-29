<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: locator.php 941 2011-10-14 09:37:23Z fatica $
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_locator'.DS.'controller.php');
require_once(JPATH_BASE . DS . 'components' . DS . 'com_locator'.DS.'models' . DS. 'directory.php');

// Create the controller
$controller = new LocatorController();

$mmciLang =& JFactory::getLanguage();
$mmciLang->load("com_locator");
    
if(LocatorModelDirectory::hasAdmin()){
	$controller->addViewPath(JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_locator' . DS . 'views');
}else{
	$controller->addViewPath(JPATH_BASE . DS . 'components' . DS . 'com_locator' . DS . 'views');	
}

//ML
$user = JFactory::getUser();

if($user->id > 0 && JRequest::getVar('task') != 'showimportcsv' && (JRequest::getVar('layout','default') == "default" || JRequest::getVar('layout') == 'import') && @JFactory::getDBO(1) === true){
	
	require_once( JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_locator' .DS.'helpers'.DS.'toolbar.php' );
	$mainframe = JFactory::getApplication();
	$mainframe->addCustomHeadTag ('<script type="text/javascript" src="includes/js/joomla.javascript.js"></script>');
	echo LocatorHelperToolbar::getToolbar();
	
}
//ML

// Register Extra tasks
$controller->registerTask( 'new'  , 	'edit' );
$controller->registerTask( 'apply', 	'save' );
$controller->registerTask( 'apply_new', 'save' );

// Perform the Request task
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));
$controller->redirect();
?>