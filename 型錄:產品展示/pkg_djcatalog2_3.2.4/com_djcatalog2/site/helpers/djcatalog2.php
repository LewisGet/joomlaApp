<?php
/**
 * @version $Id: djcatalog2.php 113 2013-01-30 12:14:25Z michal $
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

class Djcatalog2Helper {
	static $params = null;
	
	public static function getParams($reload = false) {
		if (!self::$params || $reload == true) {
			// global params
			$params = JComponentHelper::getParams( 'com_djcatalog2' );
			$app		= JFactory::getApplication();
			
			// menu specific params
			$mparams = ($app->getParams()); 
			$params->merge($mparams);
			
			// category specific params
			$option = JRequest::getVar('option');
			$view = JRequest::getVar('view');
			
			if ($option = 'com_djcatalog2' && ($view = 'item' || $view = 'items')) {
				$categories = Djc2Categories::getInstance(array('state'=>'1'));
				$category = $categories->get((int) JRequest::getVar('cid',0,'default','default'));
				if (!empty($category)) {
					$catpath = array_reverse($category->getPath());
					foreach($catpath as $k=>$v) {
						$parentCat = $categories->get((int)$v);
						if (!empty($parentCat) && !empty($category->params)) {
							$catparams = new JRegistry($parentCat->params); 
							$params->merge($catparams);
						}
					}
				}
			}
			
			$listLayout = JRequest::getVar('l');
			if ($listLayout == 'items') {
				$params->set('list_layout', 'items');
			} else if ($listLayout == 'table') {
				$params->set('list_layout', 'table');
			}
			
			$catalogMode = JRequest::getVar('cm', null);
			
			$indexSearch = JRequest::getVar('ind', null);
			
			$globalSearch = urldecode(JRequest::getVar( 'search','','default','string' ));
			$globalSearch = trim(JString::strtolower( $globalSearch ));
			if (substr($globalSearch,0,1) == '"' && substr($globalSearch, -1) == '"') { 
				$globalSearch = substr($globalSearch,1,-1);
			}
			if (strlen($globalSearch) > 0 && (strlen($globalSearch)) < 3 || strlen($globalSearch) > 20) {
				 $globalSearch = null;
			}
			if ($catalogMode === '0' || $globalSearch || $indexSearch) {
				$params->set('product_catalogue','0');
				// set 'filtering' variable in REQUEST
				// so we could hide for example sub-categories 
				// when searching/filtering is performed
				JRequest::setVar('filtering', true);
			}
			
			self::$params = $params;
		}
		return self::$params;
	}
}