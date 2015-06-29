<?php
/**
 * @version $Id: item.php 112 2013-01-29 14:44:40Z michal $
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

defined ('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelform');

class DJCatalog2ModelItem extends JModelForm {

	protected $view_item = 'item';
	protected $_item = null;
	protected $_context = 'com_djcatalog2.item';
	protected $_related = array();
	protected $_attributes = null;

	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = JRequest::getInt('id');
		$this->setState('item.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_djcatalog2.contact', 'contact', array('control' => 'jform', 'load_data' => true));
		if (empty($form)) {
			return false;
		}
		
		$user = JFactory::getUser();
		if ($user->id > 0) {
			if ($form->getValue('contact_email') == '') {
				$form->setFieldAttribute('contact_email', 'default', $user->email);
			}
			if ($form->getValue('contact_name') == '') {
				$form->setFieldAttribute('contact_name', 'default', $user->name);
			}
		}
		
		$subject = @$this->getItem()->name;
		if ($subject && $form->getValue('contact_subject') == '') {
			$form->setFieldAttribute('contact_subject', 'default', $subject);
		}
		
		return $form;
	}

	protected function loadFormData()
	{
		$data = (array)JFactory::getApplication()->getUserState('com_djcatalog2.contact.data', array());
		return $data;
	}

	public function &getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('item.id');

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {
			try
			{
				$db = JFactory::getDbo();
				$query = $db -> getQuery(true);

				$where = array();
				$attributes = $this -> getAttributes();

				$query -> select('i.*');
				$query -> select('CASE WHEN CHAR_LENGTH(i.alias) THEN CONCAT_WS(":", i.id, i.alias) ELSE i.id END as slug ');
				$query -> from('#__djc2_items as i');

				$query -> select('c.id as _category_id, c.name as category, c.published as publish_category');
				$query -> select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug ');
				$query -> join('left', '#__djc2_categories AS c ON c.id = i.cat_id');

				$query -> select('p.id as _producer_id, p.name as producer, p.published as publish_producer');
				$query -> select('CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(":", p.id, p.alias) ELSE p.id END as prodslug ');
				$query -> join('left', '#__djc2_producers AS p ON p.id = i.producer_id');

				$where[] = 'i.id ='.(int)$pk;
				$query -> where($where);
				$query -> group('i.id');
				//echo str_replace('#_','jos',$query).'<br/>';die();
				$db -> setQuery($query);
				$this->_item[$pk] = $db -> loadObject();
					
			}
			catch (JException $e)
			{
				$this->setError($e);
				$this->_item[$pk] = false;
			}

		}
		if ($this->_item[$pk])
		{
			$this->bindAttributes($pk);
		}
		return $this->_item[$pk];

	}

	function getRelatedItems($pk = null) {
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('item.id');
		
		if (empty($this->_related[$pk])) {
			$query = ' SELECT i.*, c.id AS ccategory_id, p.id AS pproducer_id, c.name AS category, p.name AS producer, p.published as publish_producer, img.fullname AS item_image, img.caption AS image_caption, '
			. ' CASE WHEN CHAR_LENGTH(i.alias) THEN CONCAT_WS(":", i.id, i.alias) ELSE i.id END as slug, '
			. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug, '
			. ' CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(":", p.id, p.alias) ELSE p.id END as prodslug '
			. ' FROM #__djc2_items AS i '
			. ' LEFT JOIN #__djc2_categories AS c ON c.id = i.cat_id '
			. ' LEFT JOIN #__djc2_producers AS p ON p.id = i.producer_id '
			. ' LEFT JOIN #__djc2_images AS img ON img.item_id = i.id AND img.type=\'item\' AND img.ordering = 1 '
			. ' WHERE i.published = 1 AND i.id IN (SELECT related_item FROM #__djc2_items_related WHERE item_id='.(int)$pk.')'
			. ' ORDER BY i.ordering ASC ';
			$this->_db->setQuery($query);
			$this->_related[$pk] = $this->_db->loadObjectList();
		}
		return $this->_related[$pk];
	}
	function getAttributes() {
		if (!$this->_attributes) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('f.*, group_concat(fo.id separator \'|\') as options');
			$query->from('#__djc2_items_extra_fields as f');
			$query->join('LEFT', '#__djc2_items_extra_fields_options as fo ON fo.field_id=f.id');
				
			$query->where('(f.visibility = 1 or f.visibility = 3) and f.published = 1');
			$query->group('f.id');
			$query->order('f.ordering asc');
			$db->setQuery($query);
			$this->_attributes = $db->loadObjectList();
		}

		return $this->_attributes;
	}
	function bindAttributes($id) {
		if (!empty($this->_item[$id])) {
			$db = JFactory::getDbo();
			
			$query_int = $db->getQuery(true);
			$query_text = $db->getQuery(true);
			
			$query_int->select('fields.alias, fields.type, fields.ordering, fieldvalues.item_id, fieldvalues.field_id, fieldvalues.id as value_id, fieldoptions.id as option_id, fieldoptions.value');
			$query_int->from('#__djc2_items_extra_fields_values_int as fieldvalues');
			$query_int->join('inner', '#__djc2_items as items on items.id=fieldvalues.item_id' );
			$query_int->join('inner','#__djc2_items_extra_fields as fields ON fields.id = fieldvalues.field_id');
			$query_int->join('left','#__djc2_items_extra_fields_options as fieldoptions ON fieldoptions.id = fieldvalues.value AND fieldoptions.field_id = fields.id');
			$query_int->where('fieldvalues.item_id='.$id.' AND (fields.visibility = 1 OR fields.visibility = 3) AND fields.published = 1');
			$query_int->order('fields.ordering asc, fieldoptions.ordering asc');
			
			$query_text->select('fields.alias, fields.type, fields.ordering, fieldvalues.item_id, fieldvalues.field_id, fieldvalues.id as value_id, 0 as option_id, fieldvalues.value');
			$query_text->from('#__djc2_items_extra_fields_values_text as fieldvalues');
			$query_text->join('inner', '#__djc2_items as items on items.id=fieldvalues.item_id' );
			$query_text->join('inner','#__djc2_items_extra_fields as fields ON fields.id = fieldvalues.field_id');
			$query_text->where('fieldvalues.item_id='.$id.' AND (fields.visibility = 1 OR fields.visibility = 3) AND fields.published = 1');
			$query_text->order('fields.ordering asc');
			
			$db->setQuery($query_int);
			$int_attributes = $db->loadObjectList();
			$db->setQuery($query_text);
			$text_attributes = $db->loadObjectList();
			
			
			foreach ($text_attributes as $attribute) {
				if ($attribute->item_id == $id) {
					$field = $attribute->alias;
					$this->_item[$id]->$field = $attribute->value;
				}
			}
			foreach ($int_attributes as $attribute) {
				if ($attribute->item_id == $id) {
					$field = $attribute->alias;
					if (!isset($this->_item[$id]->$field) || !is_array($this->_item[$id]->$field)) {
						$this->_item[$id]->$field = array();
					}
					if (!in_array($attribute->value, $this->_item[$id]->$field)) {
						$tmp_arr = $this->_item[$id]->$field;
						$tmp_arr[] = $attribute->value;
						$this->_item[$id]->$field = $tmp_arr;
					}
				}
			}
		}
	}
}