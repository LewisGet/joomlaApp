<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: view.php 941 2011-10-14 09:37:23Z fatica $
 * tc
 */

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class LocatorView extends JView
{
	function __construct($config = array())
	{
		
		parent::__construct($config);
	}
}
?>