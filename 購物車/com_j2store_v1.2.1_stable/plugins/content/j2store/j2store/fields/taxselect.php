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

// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * TaxSelect Form Field class for the J2Store component
 */
class JFormFieldTaxSelect extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'TaxSelect';
	
	protected function getInput()
 {
	//function fetchElement($name, $value, &$node, $control_name){
	
 	//$this->name, $this->value, $this->fieldname 
 		
 	
	//	$fieldName = $control_name.'['.$name.']';
		
		//$document = & JFactory::getDocument();
		//$document->addScriptDeclaration($js);
		//$document->addStyleDeclaration($css);
		
		$lists = $this->_getSelectProfiles($this->name, $this->id,$this->value);
		
		return $lists;
	
	}
	
	function _getSelectProfiles($var, $id, $default) {
		
		$db = &JFactory::getDBO();
		$option ='';
		
		$query = 'select id as value, taxprofile_name as text from #__j2store_taxprofiles order by id';		
		$db->setQuery( $query );
		$taxprofiles = $db->loadObjectList();
		
		$types[] 		= JHTML::_('select.option',  '0', '- '. JText::_( 'J2STORE_PLG_CONTENT_SELECT_TAX' ) .' -' );
		foreach( $taxprofiles as $item )
		{
			$types[] = JHTML::_('select.option',  $item->value, JText::_( $item->text ) );
		}		
		
		$lists 	= JHTML::_('select.genericlist',  $types, $var, 'class="inputbox" size="1" '.$option.'', 'value', 'text', $default );
	
		return $lists;
	
	}
}
