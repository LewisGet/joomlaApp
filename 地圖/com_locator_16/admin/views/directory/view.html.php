<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

defined( '_JEXEC' ) or die( 'Restricted access' );


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if(file_exists(JPATH_COMPONENT_SITE.DS.'view.php')){
	require_once (JPATH_COMPONENT_SITE.DS.'view.php');
}

/**
 * HTML Article View class for the Content component
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */

require_once(JPATH_COMPONENT_SITE . DS . 'views' . DS . 'directory' . DS . 'view.html.php');