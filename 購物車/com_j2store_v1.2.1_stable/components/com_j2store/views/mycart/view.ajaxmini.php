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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the J2Store component
 *
 * @static
 * @package		Joomla
 * @subpackage	J2Store
 * @since 1.0
 */
class J2StoreViewMyCart extends JView
{
	function display($tpl = null)
	{
		$mainframe = &Jfactory::getApplication();
		$model		= &$this->getModel();
		$params = &JComponentHelper::getParams('com_j2store');
		$items	=& $this->get('Data');
		
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('model',		$model);
		$this->setLayout( 'ajaxmini' );
		parent::display($tpl);

		$mainframe->close();

	}

}
?>
