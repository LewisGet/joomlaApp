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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class J2StoreViewInfo extends JView
{

	function display($tpl = null) {
		
		$mainframe = &JFactory::getApplication();
		$option = 'com_j2store';

		jimport ( 'joomla.filesystem.file' );
		$user = & JFactory::getUser();
		$db = & JFactory::getDBO();
		$db_version = $db->getVersion();
		$php_version = phpversion();
		$server = $this->get_server_software();
		$gd_check = extension_loaded('gd');
		$mb_check = extension_loaded('mbstring');

		$uri	=& JFactory::getURI();
		$model = &$this->getModel();
		
		$this->assignRef('server',$server);
		$this->assignRef('php_version',$php_version);
		$this->assignRef('db_version',$db_version);
		$this->assignRef('gd_check',$gd_check);
		$this->assignRef('mb_check',$mb_check);
		
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		
		$params = &JComponentHelper::getParams('com_j2store');
		
		$this->addToolBar();
		J2StoreSubmenuHelper::addSubmenu($vName = 'cpanel');		
		parent::display($tpl);
	}
	
	function get_server_software()
	{
		if (isset($_SERVER['SERVER_SOFTWARE'])) {
			return $_SERVER['SERVER_SOFTWARE'];
		} else if (($sf = getenv('SERVER_SOFTWARE'))) {
			return $sf;
		} else {
			return JText::_( 'n/a' );
		}
	}
	
	function addToolBar() {
		JToolBarHelper::title(JText::_('J2 Store Info'),'j2store-logo');		
	}

}
