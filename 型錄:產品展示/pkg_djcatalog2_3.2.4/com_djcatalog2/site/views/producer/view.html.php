<?php
/**
 * @version $Id: view.html.php 99 2013-01-08 10:39:32Z michal $
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

class DJCatalog2ViewProducer extends JViewLegacy {
	function display($tpl = null) {
		JHTML::_( 'behavior.modal' );
		$app = JFactory::getApplication();
		
		$document= JFactory::getDocument();
		
		$model = $this->getModel();
		$params = Djcatalog2Helper::getParams();
	   	$menus = $app->getMenu('site');
		$menu  = $menus->getActive();
		$dispatcher	= JDispatcher::getInstance();
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		$categories = Djc2Categories::getInstance(array('state'=>'1'));
		
		$item = $model->getData();
		
		/* If Item not published set 404 */
		if ($item->id == 0 || !$item->published)
		{
			throw new Exception(JText::_('COM_DJCATALOG2_PRODUCT_NOT_FOUND'), 404);
		}
		

		/* plugins */
		JPluginHelper::importPlugin('djcatalog2');
		$results = $dispatcher->trigger('onPrepareItemDescription', array (& $item, & $params, $limitstart));
		
		$this->assignref('categories', $categories);
		$this->assignref('item', $item);
		$this->assignref('images', $images);
		$this->assignref('params', $params);
		$this->_prepareDocument();
		parent::display($tpl);
	}
	protected function _prepareDocument() {
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;
		$heading		= null;

		$menu = $menus->getActive();
		
		$cid = (int) @$menu->query['cid'];
		$pid = (int) @$menu->query['pid'];
		
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		$title = $this->params->get('page_title', '');

		if ($menu && ($menu->query['option'] != 'com_djcatalog2' || $menu->query['view'] == 'item' || $menu->query['view'] == 'items' || $pid != $this->item->id)) {
			
			if ($this->item->metatitle) {
				$title = $this->item->metatitle;
			}
			else if ($this->item->name) {
				$title = $this->item->name;
			}
			$path = array(array('title' => $this->item->name, 'link' => ''));

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