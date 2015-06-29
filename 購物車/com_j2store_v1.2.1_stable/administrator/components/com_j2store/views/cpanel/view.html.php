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

class J2StoreViewCpanel extends JView
{

	function display($tpl = null) {

		$model = &$this->getModel();

		$params = &JComponentHelper::getParams('com_j2store');

		$xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'manifest.xml';


		$data = JApplicationHelper::parseXMLInstallFile($xmlfile);
			foreach($data as $key => $value) {
						$row->$key = $value;
				}
		$output=null;

		$db = &JFactory::getDbo();
		$query = 'SELECT * FROM #__updates WHERE element='.$db->quote('com_j2store');
		$db->setQuery($query);
		$wiupdate = $db->loadObject();

        $this->assignRef('params', $params);
        $this->assignRef('row', $row);
		$this->assignRef('wiupdate', $wiupdate);

		$user = & JFactory::getUser();

		$this->addToolBar();
		J2StoreSubmenuHelper::addSubmenu($vName = 'cpanel');
		parent::display($tpl);
	}

	function addToolBar() {
		JToolBarHelper::title(JText::_('Dashboard'),'j2store-logo');
		JToolBarHelper::preferences('com_j2store', '500', '850');
	}

}
