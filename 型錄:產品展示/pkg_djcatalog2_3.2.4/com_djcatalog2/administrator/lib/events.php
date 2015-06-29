<?php
/**
 * @version $Id: events.php 112 2013-01-29 14:44:40Z michal $
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

// No direct access.
defined('_JEXEC') or die;

class Djcatalog2Event extends JEvent {

	public function onItemAfterSave( $context, $table, $isNew ) {
		$params = JComponentHelper::getParams( 'com_djcatalog2' );
		$app = JFactory::getApplication();
		$data		= JRequest::getVar('jform', array(), 'post', 'array');
		
		// saving additional categories
		$db = JFactory::getDbo();
		$db->setQuery('DELETE FROM #__djc2_items_categories WHERE item_id=\''.$table->id.'\'');
		if ($db->query()) {
			if (!isset($data['categories'])) {
				$data['categories'] = array();
			}
			$data['categories'][] = $table->cat_id;
			if (!empty($data['categories'])) {
				$data['categories'] = array_unique($data['categories']);
				foreach ($data['categories'] as $cat_id) {
					$db->setQuery('INSERT INTO #__djc2_items_categories VALUES (\''.$table->id.'\', \''.$cat_id.'\')');
					$db->query();
				}
			}
		}
		
		// saving images
		if (!DJCatalog2ImageHelper::saveImages('item',$table, $params, $isNew)) {
			$app->enqueueMessage(JText::_('COM_DJCATALOG2_ERROR_SAVING_IMAGES'),'error');
		}
		
		// saving attachments
		if (!DJCatalog2FileHelper::saveFiles('item',$table, $params, $isNew)) {
			$app->enqueueMessage(JText::_('COM_DJCATALOG2_ERROR_SAVING_FILES'),'error');
		}
		
		// saving additional attributes
		$attribs = JRequest::getVar('attribute',array(),'post','array');
		$model = JModelAdmin::getInstance('Item', 'Djcatalog2Model', array());
		if (!$model->saveAttributes($attribs, $table)) {
			$app->enqueueMessage($model->getError(),'error');
		}
	}
	public function onItemAfterDelete( $context, $table) {
		$params = JComponentHelper::getParams( 'com_djcatalog2' );
		$app = JFactory::getApplication();
		
		$db = JFactory::getDbo();
		$db->setQuery('DELETE FROM #__djc2_items_categories WHERE item_id=\''.$table->id.'\'');
		$db->query();
		
		$db->setQuery('DELETE FROM #__djc2_items_related WHERE item_id=\''.$table->id.'\' OR related_item=\''.$table->id.'\'');
		$db->query();
		
		$db->setQuery('DELETE FROM #__djc2_items_extra_fields_values_text WHERE item_id=\''.$table->id.'\'');
		$db->query();
		
		$db->setQuery('DELETE FROM #__djc2_items_extra_fields_values_int WHERE item_id=\''.$table->id.'\'');
		$db->query();
		
		if (!DJCatalog2ImageHelper::deleteImages('item',$table->id)) {
			$app->enqueueMessage(JText::_('COM_DJCATALOG2_ERROR_DELETING_IMAGES'),'error');
		}
		if (!DJCatalog2FileHelper::deleteFiles('item',$table->id)) {
			$app->enqueueMessage(JText::_('COM_DJCATALOG2_ERROR_DELETING_FILE'),'error');
		}
	}
	public function onCategoryAfterSave( $context, $table, $isNew ) {
		$params = JComponentHelper::getParams( 'com_djcatalog2' );
		$app = JFactory::getApplication();
		if (!DJCatalog2ImageHelper::saveImages('category',$table, $params, $isNew)) {
			$app->enqueueMessage(JText::_('COM_DJCATALOG2_ERROR_SAVING_IMAGES'),'error');
		}
	}
	public function onCategoryAfterDelete( $context, $table) {
		$params = JComponentHelper::getParams( 'com_djcatalog2' );
		$app = JFactory::getApplication();
		if (!DJCatalog2ImageHelper::deleteImages('category',$table->id)) {
			$app->enqueueMessage(JText::_('COM_DJCATALOG2_ERROR_DELETING_IMAGES'),'error');
		}
	}
	public function onProducerAfterSave( $context, $table, $isNew ) {
		$params = JComponentHelper::getParams( 'com_djcatalog2' );
		$app = JFactory::getApplication();
		if (!DJCatalog2ImageHelper::saveImages('producer',$table, $params, $isNew)) {
			$app->enqueueMessage(JText::_('COM_DJCATALOG2_ERROR_SAVING_IMAGES'),'error');
		}
	}
	public function onProducerAfterDelete( $context, $table) {
		$params = JComponentHelper::getParams( 'com_djcatalog2' );
		$app = JFactory::getApplication();
		if (!DJCatalog2ImageHelper::deleteImages('producer',$table->id)) {
			$app->enqueueMessage(JText::_('COM_DJCATALOG2_ERROR_DELETING_IMAGES'),'error');
		}
	}
	public function onFieldAfterSave( $context, $table, $isNew ) {
		$params = JComponentHelper::getParams( 'com_djcatalog2' );
		$app = JFactory::getApplication();
		$values = (JRequest::getVar('fieldtype',array(),'default','array'));
		$model = JModelAdmin::getInstance('Field', 'Djcatalog2Model', array());
		if (!$model->saveOptions($values, $table)) {
			$app->enqueueMessage($model->getError(),'error');
		}
	}
	public function onFieldAfterDelete( $context, $table) {
		$params = JComponentHelper::getParams( 'com_djcatalog2' );
		$app = JFactory::getApplication();
		$model = JModelAdmin::getInstance('Field', 'Djcatalog2Model', array());
		if (!$model->deleteOptions($table)) {
			$app->enqueueMessage($model->getError(),'error');
		}
	}
}