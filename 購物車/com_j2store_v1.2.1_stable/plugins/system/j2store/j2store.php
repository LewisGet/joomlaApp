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

jimport( 'joomla.plugin.plugin' );
jimport('joomla.html.parameter');

class plgSystemJ2Store extends JPlugin {

	function plgSystemJ2Store( &$subject, $config ){
		parent::__construct( $subject, $config );
		$this->_plugin = JPluginHelper::getPlugin( 'system', 'j2store' );
        $this->_params = new JParameter( $this->_plugin->params );
		//if($this->_mainframe->isAdmin())return;

	}

	function onAfterRoute() {

		$mainframe = &JFactory::getApplication();
		JHtml::_('behavior.framework');
		JHtml::_('behavior.modal');
		$baseURL = JURI::root();
		$j2storeparams = &JComponentHelper::getParams('com_j2store');
		$document =& JFactory::getDocument();
		if($mainframe->isAdmin()) {
			$document->addScript($baseURL.'administrator/components/com_j2store/js/j2store_admin.js');
		}
		else {
		$document->addScriptDeclaration("var j2storeURL = '$baseURL';");
		$document->addScript($baseURL.'components/com_j2store/js/j2store.js');


		// Add related CSS to the <head>
		if ($document->getType() == 'html' && $j2storeparams->get('j2store_enable_css')) {

			jimport('joomla.filesystem.file');

			// j2store.css
			if(JFile::exists(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'css'.DS.'j2store.css'))
				$document->addStyleSheet(JURI::root(true).'/templates/'.$mainframe->getTemplate().'/css/j2store.css');
			else
				$document->addStyleSheet(JURI::root(true).'/components/com_j2store/css/j2store.css');

		} else {
			$document->addStyleSheet(JURI::root(true).'/components/com_j2store/css/j2store.css');
		}

		}
	}

}
