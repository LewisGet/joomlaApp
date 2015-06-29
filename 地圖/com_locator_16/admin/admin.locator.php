<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 * tc 
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php' );

$lang =& JFactory::getLanguage();

$lang->load( 'com_locator', JPATH_SITE );	

$controller = new LocatorController();


$controller->addModelPath(JPATH_COMPONENT_SITE.DS.'models');

// Perform the Request task
$controller->execute(JRequest::getVar('task', 'admin', 'default', 'cmd'));

$controller->redirect();

?>