<?php
/**
 * @version $Id: view.html.php 114 2013-02-01 10:04:49Z michal $
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

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class DJCatalog2ViewItem extends JViewLegacy {
	function display($tpl = null) {
		JHTML::_( 'behavior.modal' );
		$app = JFactory::getApplication();
		$document= JFactory::getDocument();
		$model = $this->getModel();
		
		$menus		= $app->getMenu('site');
		$menu  = $menus->getActive();
		$dispatcher	= JDispatcher::getInstance();
		
		$categories = Djc2Categories::getInstance(array('state'=>'1'));
		
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		
		$state = $this->get('State');
		$item = $this->get('Item');
		$this->contactform	= $this->get('Form');
		$this->showcontactform = ($app->getUserState('com_djcatalog2.contact.data')) ? 'false' : 'true';
		
		$catid = (int)JRequest::getVar('cid');
		$category = $categories->get($item->cat_id);
		$current_category = ($catid == $item->cat_id) ? $category : $categories->get($catid);
		
		if (empty($item) || !$item->published) {
			throw new Exception(JText::_('COM_DJCATALOG2_PRODUCT_NOT_FOUND'), 404);
			//return JError::raiseError( 404, JText::_( 'COM_DJCATALOG2_PRODUCT_NOT_FOUND') );
		}
		
		if (($current_category && $current_category->id > 0 && $current_category->published == 0) || empty($category)) {
			if (($category && $category->id > 0 && $category->published == 0) || empty($category))
			{
				throw new Exception(JText::_('COM_DJCATALOG2_PRODUCT_NOT_FOUND'), 404);
			}
		}
		
		// if category id in the URL differs from product's category id
		// we add canonical link to document's header
		if (DJCatalogHelperRoute::getItemRoute($item->slug, (int)$item->cat_id) != DJCatalogHelperRoute::getItemRoute($item->slug, (int)$catid)) {
			$document->addCustomTag('<link rel="canonical" href="'.JRoute::_(DJCatalogHelperRoute::getItemRoute($item->slug, $item->catslug)).'"/>');
		}
		
		// if category id is not present in the URL or it equals 0
		// we set it to product's cat id
		if ($catid == 0) {
			JRequest::setVar('cid', $item->cat_id);
		}
		
		// params in this view should be generated only after we make sure
		// that product's cat id is in the request.
		$params = Djcatalog2Helper::getParams();
		
		/* plugins */
		JPluginHelper::importPlugin('djcatalog2');
		
		$results = $dispatcher->trigger('onPrepareItemDescription', array (& $item, & $params, $limitstart));
		
		$item->event = new stdClass();
		$resultsAfterTitle = $dispatcher->trigger('onAfterDJCatalog2DisplayTitle', array (&$item, &$params, $limitstart));
		$item->event->afterDJCatalog2DisplayTitle = trim(implode("\n", $resultsAfterTitle));

		$resultsBeforeContent = $dispatcher->trigger('onBeforeDJCatalog2DisplayContent', array (&$item, &$params, $limitstart));
		$item->event->beforeDJCatalog2DisplayContent = trim(implode("\n", $resultsBeforeContent));

		$resultsAfterContent = $dispatcher->trigger('onAfterDJCatalog2DisplayContent', array (&$item, &$params, $limitstart));
		$item->event->afterDJCatalog2DisplayContent = trim(implode("\n", $resultsAfterContent));

		$this->assignref('categories', $categories);
		$this->assignref('category', $category);
		$this->assignref('item', $item);
		$this->assignref('images', $images);
		$this->assignref('files', $files);
		
		$this->assignref('params', $params);
		$this->relateditems = $model->getRelatedItems();
		$this->attributes = $model->getAttributes();
		$this->_prepareDocument();
		
		parent::display($tpl);
	}
	protected function _prepareDocument() {
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;
		$heading		= null;
		$document= JFactory::getDocument();
		$menu = $menus->getActive();
		
		$id = (int) @$menu->query['id'];
		$cid = (int) @$menu->query['cid'];
		
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		$title = $this->params->get('page_title', '');

		if ($menu && ($menu->query['option'] != 'com_djcatalog2' || $menu->query['view'] == 'items' || $id != $this->item->id )) {
			
			if ($this->item->metatitle) {
				$title = $this->item->metatitle;
			}
			else if ($this->item->name) {
				$title = $this->item->name;
			}
			$category = $this->categories->get($this->item->cat_id);
			$path = array(array('title' => $this->item->name, 'link' => ''));
			while (($menu->query['option'] != 'com_djcatalog2' || ($menu->query['view'] == 'items' && $cid != $category->id)) && $category->id > 0)
			{
				$path[] = array('title' => $category->name, 'link' => DJCatalogHelperRoute::getCategoryRoute($category->catslug));
				$category = $this->categories->get($category->parent_id);
			}

			$path = array_reverse($path);

			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
			
		}
		
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			if ($app->getCfg('sitename_pagetitles', 0) == '2') {
				$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
			} else {
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
			}
		}
		$this->document->setTitle($title);

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description')) 
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords')) 
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}

?>