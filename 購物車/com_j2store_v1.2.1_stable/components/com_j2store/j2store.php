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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the base controller
require_once (JPATH_COMPONENT.DS.'helpers'.DS.'utilities.php');
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'prices.php');
//JHTML::_('stylesheet', 'j2store.css', 'components/com_j2store/css/');
JHtml::_('behavior.framework');

//JHTML::_('behavior.mootools');

// Require specific controller if requested
$controller = JRequest::getWord('view', 'mycart');
$task = JRequest::getWord('task');

jimport('joomla.filesystem.file');
jimport('joomla.html.parameter');

if (JFile::exists(JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php')) {
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
	$classname = 'j2storeController'.$controller;
	$controller = new $classname();
	$controller->execute($task);
	$controller->redirect();
}
else {
	JError::raiseError(404, JText::_('j2store_NOT_FOUND'));
}
