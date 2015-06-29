<?php
/**
 * @version $Id: djproducer.php 87 2012-08-02 12:13:07Z michal $
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


class JFormFieldDjproducer extends JFormField {
	
	protected $type = 'Djproducer';
	
	protected function getInput()
	{
		$attr = '';

		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		
		$db	= JFactory::getDBO();
		$query = "SELECT * FROM #__djc2_producers ORDER BY name";
		
		$db->setQuery($query);
		$producers = $db->loadObjectList();
		
		$options = array();
		$options[] = JHTML::_('select.option', '','- '.JText::_('COM_DJCATALOG2_SELECT_PRODUCER').' -' );
		foreach($producers as $producer){
			$options[] = JHTML::_('select.option', $producer->id, $producer->name);
			
		}
		$out = JHTML::_('select.genericlist', $options, $this->name, null, 'value', 'text', $this->value);
		
		return ($out);
	}
}
?>