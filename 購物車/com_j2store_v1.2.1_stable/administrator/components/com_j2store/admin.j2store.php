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
jimport('joomla.application.component.controller');
JHTML::_('stylesheet', 'style.css', 'administrator/components/com_j2store/css/');
require_once (JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'helpers'.DS.'utilities.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'submenu.php');
/*
 * Make sure the user is authorized to view this page
*/
$controller = JRequest::getWord('view', 'cpanel');
if (JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.$controller.'.php')
		&& $controller !='countries' && $controller !='zones'
		&& $controller !='country' && $controller !='zone'
)

{
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');

	$classname = 'J2StoreController'.$controller;
	$controller = new $classname();

} else {
	$controller = JController::getInstance('J2Store');
}
$controller->execute(JRequest::getWord('task'));
$controller->redirect();