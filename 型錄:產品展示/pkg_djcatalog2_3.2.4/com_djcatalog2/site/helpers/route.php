<?php
/**
 * @version $Id: route.php 110 2013-01-28 10:38:36Z michal $
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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.helper');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_djcatalog2'.DS.'lib'.DS.'categories.php');

class DJCatalogHelperRoute
{
	protected static $lookup;
	public static function getItemRoute($id, $catid = 0)
	{
		$needles = array(
			'item'  => array((int) $id)
		);
		//Create the link
		$link = 'index.php?option=com_djcatalog2&view=item&id='. $id;
		if ((int)$catid >= 0)
		{
			$categories = Djc2Categories::getInstance(array('state'=>'1'));
			$category = $categories->get((int)$catid);
			if($category)
			{
				$path = $category->getPath();
				$path[] = 'catalog';
				$needles['items'] = ($path);
				$link .= '&cid='.$catid;
			}
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}

		return $link;
	}
	
	public static function getProducerRoute($id)
	{
		$needles = array(
			'producer'  => array((int) $id),
			'items'  => array(0)
		);
		$link = 'index.php?option=com_djcatalog2&view=producer&pid='. $id;

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	public static function getCategoryRoute($catid)
	{
		$needles = array(
			'items'  => array((int) $catid)
		);
		
		//Create the link
		$link = 'index.php?option=com_djcatalog2&view=items';
		if ((int)$catid >= 0)
		{
			$categories = Djc2Categories::getInstance(array('state'=>'1'));
			$category = $categories->get((int)$catid);
			if($category)
			{
				$path = $category->getPath();
				$path[] = '0:all';
				$needles['items'] = ($path);
				$link .= '&cid='.$catid;
			}
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		return $link;
	}
	protected static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_djcatalog2');
			$items		= $menus->getItems('component_id', $component->id);
			if (count($items)) {
				foreach ($items as $item)
				{
					if (isset($item->query) && isset($item->query['view']))
					{
						$parameter = 'id';
						if ($item->query['view'] == 'items') {
							$parameter = 'cid';
						}
						else if ($item->query['view'] == 'producer') {
							$parameter = 'pid';
						}
						$view = $item->query['view'];
						if (!isset(self::$lookup[$view])) {
							self::$lookup[$view] = array();
						}
						if (isset($item->query[$parameter])) {
							self::$lookup[$view][$item->query[$parameter]] = $item->id;
						}
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view]))
				{
					foreach($ids as $id)
					{
						if (isset(self::$lookup[$view][(int)$id])) {
							return self::$lookup[$view][(int)$id];
						}
					}
				}
			}
		}
		//else {
		$active = $menus->getActive();
		if ($active && $active->component == 'com_djcatalog2') {
			return $active->id;
		} else {
			$default = $menus->getDefault();
			return $default->id;
		}
		//}

		return null;
	}
}
?>
