<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (JPATH_COMPONENT_SITE.DS.'view.php');

/**
 * HTML Article View class for the Content component
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class LocatorViewSearch extends LocatorView 
{
	
	function display($tpl = null){

		$total = 0;
		$items = null;
		
		// Get some data from the model
		$items		= & $this->get( 'Data');
		$total		= & $this->get( 'Total');		
		
		$state =& $this->get( 'state' );

		$lists		= & $this->get( 'Lists');

		//assign template variables
		$lists['order_Dir'] = $state->get( 'filter_order_Dir' );
        $lists['order']     = $state->get( 'filter_order' );
	
		$this->items =& $items;
	
		jimport('joomla.html.pagination');
		
		$pagination = new JPagination($total, $state->get('limitstart'), $state->get('limit'));

		$this->assignRef('lists',$lists);
		$this->assign('total',			$total);
		$this->assignRef('items',$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);
		
		parent::display($tpl); 
	
	}
	
}