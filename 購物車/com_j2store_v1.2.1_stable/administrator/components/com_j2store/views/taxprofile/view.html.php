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

class J2StoreViewTaxProfile extends JView
{

	function display($tpl = null) {
		
		global $mainframe, $option;
		$db		=& JFactory::getDBO();
		$uri	=& JFactory::getURI();
		$model		= &$this->getModel('taxprofile');
		$params = &JComponentHelper::getParams('com_j2store');
		// get order data
		$taxprofile	= & $this->get('Data');
		$isNew		= ($taxprofile->id < 1);
		
		if($isNew) {			
			$taxprofile->published = 1;
			
		}
		
		$lists = array();
		$arr = array(JHTML::_('select.option', '0', JText::_('No') ),
					JHTML::_('select.option', '1', JText::_('Yes') )	);
		$lists['published'] = JHTML::_('select.genericlist', $arr, 'published', null, 'value', 'text', $taxprofile->published);
		
		$this->assignRef('taxprofile',	$taxprofile);
		$this->assignRef('lists',	$lists);
		$this->assignRef('params',	$params);
		
		$this->addToolBar();
		J2StoreSubmenuHelper::addSubmenu($vName = 'taxprofiles');
	
		parent::display($tpl);
	}
	
	function addToolBar() {
		
		JToolBarHelper::title(JText::_('Edit Tax Profile'),'j2store-logo');
		
		// Set toolbar items for the page
		$edit		= JRequest::getVar('edit',true);
		$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Tax Profile' ).': <small><small>[ ' . $text.' ]</small></small>' );
		JToolBarHelper::save();
		if (!$edit)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}	
		
	}

}
