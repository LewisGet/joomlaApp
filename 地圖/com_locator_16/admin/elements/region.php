<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$ ?
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');//import the necessary class definition for formfield


/**
 * Supports an HTML select list of articles
 * @since  1.6
 */

if(class_exists('JFormField')){

	class JFormFieldRegion extends JFormField
	{
		
		/**
		* The form field type.
		*
		* @var  string
		* @since	1.6
		*/
		protected $type = 'region'; //the form field type
	
		/**
		* Method to get content articles
		*
		* @return	array	The field option objects.
		* @since	1.6
		*/
		protected function getInput()
		{
			// Initialize variables.
			$session = JFactory::getSession();
			$options = array();
			
			$attr = '';
			
			// Initialize some field attributes.
			$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
			
			// To avoid user's confusion, readonly="true" should imply disabled="true".
			if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
				$attr .= ' disabled="disabled"';
			}
			
			$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
			$attr .= $this->multiple ? ' multiple="multiple"' : '';
			
			// Initialize JavaScript field attributes.
			$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
				
			//now get to the business of finding the articles		
			$db = &JFactory::getDBO();

			$sql = "SELECT distinct region as region FROM #__location_world_boundaries  ORDER BY region";
	
			$options = array();
			
			$db->setQuery( $sql );
			
			$options = $db->loadObjectList();
							  
			return JHTML::_('select.genericlist',  $options, $this->name, trim($attr), 'region', 'region', $this->value );
	
		  
		}
	}
}

if(class_exists('JElement')){
	
	class JElementRegion extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var	$_name = 'region';
	
		
		function fetchElement($name, $value, &$node, $control_name)
		{
	
			         // Base name of the HTML control.
	                $ctrl  = $control_name .'['. $name .']';

	                $sql = "SELECT distinct region as region FROM #__location_world_boundaries  ORDER BY region";
	               
					$db = JFactory::getDBO();
					$db->setQuery($sql);
					$dboptions = $db->loadObjectList();
	 
	                // Construct an array of the HTML OPTION statements.
	                $options = array ();
	                
	                if(count($dboptions) > 0){
	                	
		                foreach ($dboptions as $option)
		                {
		                	
		                        $val   = $option->region;
		                        $text  = $option->region;
		                        $options[] = JHTML::_('select.option', $val, JText::_($text));
		                }
	                }
	 
	                // Construct the various argument calls that are supported.
	                $attribs       = ' ';
	                if ($v = $node->attributes( 'size' )) {
	                        $attribs       .= 'size="'.$v.'"';
	                }
	                if ($v = $node->attributes( 'class' )) {
	                        $attribs       .= 'class="'.$v.'"';
	                } else {
	                        $attribs       .= 'class="inputbox"';
	                }
	                if ($m = $node->attributes( 'multiple' ))
	                {
	                        $attribs       .= ' multiple="multiple"';
	                        $ctrl          .= '[]';
	                }
	                
	                // Render the HTML SELECT list.
	                return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value );
	//JHTML::_('select.genericlist',  $tag_display, 'tags', 'class="inputbox" size="1"', 'id', 'name',$selected);
		}
	}
}