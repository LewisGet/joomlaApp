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

class J2StoreViewShippingMethods extends JView
{

	function display($tpl = null) {
		
		$mainframe = &JFactory::getApplication();
		$option = 'com_j2store';
		$ns = 'com_j2store.shippingmethods';
		$task = JRequest::getVar('task');
				
		if($task == 'add'|| $task == 'edit') {
			$this->showForm($tpl);
		} else {
			

		$db		=& JFactory::getDBO();
		$uri	=& JFactory::getURI();
		$params = &JComponentHelper::getParams('com_j2store');
	
		$filter_order		= $mainframe->getUserStateFromRequest( $ns.'filter_order',		'filter_order',		'a.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $ns.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$filter_orderstate	= $mainframe->getUserStateFromRequest( $ns.'filter_orderstate',	'filter_orderstate',	'', 'string' );

		$search				= $mainframe->getUserStateFromRequest( $ns.'search',			'search',			'',				'string' );
		if (strpos($search, '"') !== false) {
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);

		// Get data from the model
		$items		= & $this->get( 'Item');
		$total		= & $this->get( 'Total');
		$pagination = & $this->get( 'Pagination' );

		$javascript 	= 'onchange="document.adminForm.submit();"';
	
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search']= $search;

		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);
		//adding toolbar
		JToolBarHelper::title(JText::_('Shipping Methods'),'j2store-logo');
		JToolBarHelper::addNewX();
		JToolBarHelper::editListX();
		JToolBarHelper::deleteList();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();

		}
		//$this->addToolBar();
		J2StoreSubmenuHelper::addSubmenu($vName = 'shippingmethods');
		
		


		parent::display($tpl);
	}
	
	
	function showForm($tpl) {
		
		$item		= & $this->get('Data');
		$this->assignRef('item',		$item);

		$isNew		= ($item->id < 1);
		
		if($isNew) {			
			$item->published = 1;
			
		}
		
		$lists = array();
		//$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class=""', $item->published );
		$arr = array(JHTML::_('select.option', '0', JText::_('No') ),
				JHTML::_('select.option', '1', JText::_('Yes') )	);
		$lists['published'] = JHTML::_('select.genericlist', $arr, 'published', null, 'value', 'text', $item->published );
		
		
		$this->assignRef('shippingmethods',	$item);
		$this->assignRef('lists',	$lists);
		
		
		//adding toolbar
		JToolBarHelper::title(JText::_('Edit Shipping method'),'j2store-logo');
		
		// Set toolbar items for the page
		$edit		= JRequest::getVar('edit',true);
		$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Shipping method' ).': <small><small>[ ' . $text.' ]</small></small>','j2store-logo' );
		JToolBarHelper::save();
		if (!$edit)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}	

	}

}
