<?php
/**
 * @version $Id: djcategory.php 92 2012-11-07 10:36:34Z michal $
 * @package DJ-Catalog2
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Michal Olczyk - michal.olczyk@design-joomla.eu
 *
 * DJ-Catalog2 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-Catalog2 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-Catalog2. If not, see <http://www.gnu.org/licenses/>.
 *
 */

defined('_JEXEC') or die();
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

require_once(dirname(__FILE__).DS.'..'.DS.'..'.DS.'lib'.DS.'categories.php');

class JFormFieldDjcategory extends JFormField {
	
	protected $type = 'Djcategory';
	
	protected function getInput()
	{
		$attr = ''; 

		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->element['multiple']=='true' ? ' multiple="multiple"' : '';

		$default_name = ($this->element['default_name']) ? '- '.JText::_($this->element['default_name']).' -':null;
		
    	$categories = Djc2Categories::getInstance();
		$optionList = $categories->getOptionList($default_name, ($this->element['parent'] !='true') ? false:true, JRequest::getInt('id', null), ($this->element['default_disable'] == 'true') ? true:false  );
		$html = JHTML::_('select.genericlist', $optionList, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		
		return ($html);
		
	}
}
?>