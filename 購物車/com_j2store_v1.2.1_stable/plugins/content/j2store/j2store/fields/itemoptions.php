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
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'popup.php');

/**
 * TaxSelect Form Field class for the J2Store component
 */
class JFormFieldItemOptions extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'ItemOptions';

function getInput() { 

//fetchElement($name, $value, &$node, $control_name){
		
		$fieldName = $this->fieldname;
		
		//get libraries
		$html ='';
		
		$html .='<table class="j2store_itemoptions"><tr><td>';
		
		$cid = JRequest::getVar('id');
		if($cid) {
			$link = 'index.php?option=com_j2store&view=products&task=setattributes&id='.$cid.'&tmpl=component';			
			
			//let us first get Product Attribute Names
			$attributes = $this->getProductAttributes($cid);
			if(!empty($attributes)) {
				$html .=$attributes;
			}
		
				//$html .= J2StorePopup::popup( $link, JText::_( "PLG_J2STORE_ADD_REMOVE_ATTRIBUTES" ), array('onClose' => '\function(){j2storeNewModal(\''.JText::_('Saving the Product options...').'\'); Joomla.submitbutton(\'apply\');}') ); 			
				$html .= J2StorePopup::popup( $link, JText::_( "PLG_J2STORE_ADD_REMOVE_ATTRIBUTES" ) );
				$html .= JText::_('PLG_J2STORE_ITEM_OPTION_NOTE');
		} else {
			$html .= JText::_('J2STORE_PLG_CONTENT_CLICK_SAVE_FILL_ATTRIB');			
		}  
		$html .= '</td></tr></table>';		
		return $html;
	}
	
	
	function getProductAttributes($product_id) {
		
		$db = &JFactory::getDBO();
		$query = 'SELECT a.* FROM #__j2store_productattributes AS a WHERE a.product_id='. (int) $product_id
				 .' ORDER BY a.ordering'
		;
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$html= '';
		
		if(count($rows)) {
		$html .='<table width="100%" id="j2store_table">
			<thead>
			<th>'.JText::_('J2STORE_PLG_CONTENT_ATTRIB_NAME').'</th>
			<th>'.JText::_('J2STORE_PLG_CONTENT_SET_OPTIONS').'</th>
			<th>'.JText::_('J2STORE_PLG_CONTENT_OPTIONS_SET').'</th>
			<th>'.JText::_('J2STORE_PLG_CONTENT_REMOVE').'</th>
			</thead>
			<tbody>';
			foreach($rows as $row) {
				//lets get a list of attribute options for each attribute
				$pa_options = $this->getProductAttributeOptions($row->productattribute_id);
				
				$html .='<tr>';
				$html .='<td>'.$row->productattribute_name.'</td>';		
				$html .='<td>';		
				//$html .= J2StorePopup::popup( "index.php?option=com_j2store&view=products&task=setattributeoptions&id=".$row->productattribute_id."&tmpl=component", JText::_( "[ Set options ]" ), array('onclose' => '\function(){j2storeNewModal(\''.JText::_('Saving the Product options...').'\'); submitbutton(\'apply\');}')); 
				$html .= J2StorePopup::popup( "index.php?option=com_j2store&view=products&task=setattributeoptions&id=".$row->productattribute_id."&tmpl=component", '[ '.JText::_( "J2STORE_PLG_CONTENT_SET_OPTIONS" ).' ]');
				$html .='</td><td>';
				//attribute options
				if(!empty($pa_options)) {
					$html .=$pa_options;
				}
				$html .='</td><td class="j2store_trash"> <br/><br/>';
				$html .='<a href="index.php?option=com_j2store&view=products&task=deleteattributes&cid[]='.$row->productattribute_id.'&return='.base64_encode("index.php?option=com_content&view=article&&layout=edit&id=".$product_id).'"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$html .= JText::_( 'J2STORE_PLG_CONTENT_ATTRIB_DELETE' );
				$html .='</a>';
				$html .='</td>';
				$html .='</tr>';			
			}
			
			$html .='</tbody>';
			$html .='</table>';				
		}
		return $html;
	}
	
	
	function getProductAttributeOptions($pa_id) {
		
		$db = &JFactory::getDBO();
		$query = 'SELECT a.* FROM #__j2store_productattributeoptions AS a WHERE a.productattribute_id='. (int) $pa_id
				 .' ORDER BY a.ordering'
		;
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$html = '';
		foreach ($rows as $row) {
			$html .= $row->productattributeoption_name.',&nbsp;&nbsp;'; 
		}
		
		return $html;
	}
		
}
