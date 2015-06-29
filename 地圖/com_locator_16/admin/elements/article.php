<?php
/**
* @version		$Id: article.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


if(class_exists('JElement')){
		
	class JElementArticle extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var	$_name = 'Article';
	
		function fetchElement($name, $value, &$node, $control_name)
		{
			global $mainframe;
	
			$db			=& JFactory::getDBO();
			$doc 		=& JFactory::getDocument();
			$template 	= $mainframe->getTemplate();
			$fieldName	= $control_name.'['.$name.']';
			
			$article =& JTable::getInstance('content');
			if ($value) {
				$article->load($value);
			} else {
				$article->title = JText::_('Select an Article');
			}
	
			$js = "
			function jSelectArticle(id, title, object) {
				document.getElementById(object + '_id').value = id;
				document.getElementById(object + '_name').value = title;
				document.getElementById('sbox-window').close();
			}
			
			function clearArticle(object){
			
				document.getElementById(object + '_id').value = '';
				document.getElementById(object + '_name').value = '".JText::_('Select an Article')."';
				
			}
			";
			$doc->addScriptDeclaration($js);
	
			$link = 'index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;object='.$name;
	
			JHTML::_('behavior.modal', 'a.modal');
			$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
	//		$html .= "\n &nbsp; <input class=\"inputbox modal-button\" type=\"button\" value=\"".JText::_('Select')."\" />";
			$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('Select an Article').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('Select').'</a></div></div>'."\n";
			$html .= '<div class="button2-left"><div class="blank"><a title="'.JText::_('Clear').'"  href="javascript:clearArticle(\'article\');">'.JText::_('Clear').'</a></div></div>'."\n";
			$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';
	
			return $html;
		}
	}

}



jimport('joomla.html.html');
jimport('joomla.form.formfield');//import the necessary class definition for formfield


/**
 * Supports an HTML select list of articles
 * @since  1.6
 */

if(class_exists('JFormField')){
	
class JFormFieldArticle extends JFormField
{
	
	/**
	* The form field type.
	*
	* @var  string
	* @since	1.6
	*/
	protected $type = 'Article'; //the form field type

	/**
	* Method to get content articles
	*
	* @return	array	The field option objects.
	* @since	1.6
	*/
	protected function getInput()
	{
		
		$mainframe = JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$doc 		=& JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		
		$fieldName	= $this->name;
		
		$article =& JTable::getInstance('content');
		
		if ($this->value) {
			$article->load($this->value);
		} else {
			$article->title = JText::_('Select an Article');
		}
		
		$name = $this->name;
		$value = (int)$this->value;

		$js = "
		function jSelectArticle_jform_request_id(id, title, object) {
			document.getElementById('{$name}_id').value = id;
			document.getElementById('{$name}_name').value = title;
			SqueezeBox.close();
		}
		
		function clearArticle(object){
		
			document.getElementById(object + '_id').value = '';
			document.getElementById(object + '_name').value = '".JText::_('Select an Article')."';
			
		}
		";
		
		$doc->addScriptDeclaration($js);

		
		$link = 'index.php?option=com_content&view=articles&layout=modal&tmpl=component&function=jSelectArticle_jform_request_id&'.$name;

		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
//		$html .= "\n &nbsp; <input class=\"inputbox modal-button\" type=\"button\" value=\"".JText::_('Select')."\" />";
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('Select an Article').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('Select').'</a></div></div>'."\n";
		$html .= '<div class="button2-left"><div class="blank"><a title="'.JText::_('Clear').'"  href="javascript:clearArticle(\''.$name.'\');">'.JText::_('Clear').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;

	}
}

}