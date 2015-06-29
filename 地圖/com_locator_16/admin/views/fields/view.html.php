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

require_once (JPATH_COMPONENT_SITE.DS.'view.php');

/**
 * HTML Article View class for the Content component
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class LocatorViewFields extends LocatorView 
{
	
	function display($tpl = null){
		
		$total = "";
		
		$this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'fields' . DS . 'tmpl');
		
		$task = JRequest::getString('task','');	

		if($task != "addfield" ){
			// Get some data from the model
			$items		= & $this->get( 'Data');
			$total		= & $this->get( 'Total');		
	
			//assign template variables
			
			$this->items =& $items;
		}
		
		$lists  = & $this->get( 'Lists');
	
		jimport('joomla.html.pagination');

		$limit		= JRequest::getVar('limit', 10, '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$pagination = new JPagination($total, $limitstart, $limit);
	
		$this->assignRef('lists',$lists);
		$this->assign('total',			$total);
		$this->assignRef('items',$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);
		
		parent::display($tpl);
	
	}
	
}