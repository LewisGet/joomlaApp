<?php
/**
 * @version $Id: router.php 105 2013-01-23 14:05:57Z michal $
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

defined('_JEXEC') or die;

function DJCatalog2BuildRoute(&$query)
{
    $segments = array();
    
    $app        = JFactory::getApplication();
    $menu       = $app->getMenu('site');


    if (empty($query['Itemid'])) {
        $menuItem = $menu->getActive();
    } else {
        $menuItem = $menu->getItem($query['Itemid']);
    }
    $option = (empty($menuItem->component)) ? null : $menuItem->component;
    
    $mView  = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
    $mCatid = (empty($menuItem->query['cid'])) ? null : (int)$menuItem->query['cid'];
    $mProdid   = (empty($menuItem->query['pid'])) ? null : (int)$menuItem->query['pid'];
    $mId    = (empty($menuItem->query['id'])) ? null : (int)$menuItem->query['id'];
    
    $view = !empty($query['view']) ? $query['view'] : null;
    $cid = !empty($query['cid']) ? $query['cid'] : null;
    $pid = !empty($query['pid']) ? $query['pid'] : null;
    $id = !empty($query['id']) ? $query['id'] : null;
    
    // JoomSEF bug workaround
    if (isset($query['start']) && isset($query['limitstart'])) {
    	if ((int)$query['limitstart'] != (int)$query['start'] && (int)$query['start'] > 0) {
    		// let's make it clear - 'limitstart' has higher priority than 'start' parameter, 
    		// however ARTIO JoomSEF doesn't seem to respect that.
    		$query['start'] = $query['limitstart'];
    		unset($query['limitstart']);
    	}
    }
    // JoomSEF workaround - end

    if ($view && $option == 'com_djcatalog2') {
        if ($view != $mView) {
            $segments[] = $view;
        }
        
        unset($query['view']);
        
    	if ($view == 'item') {
        	if ($view == $mView && intval($id) > 0 && intval($id) == $mId) {
        		unset($query['id']);
        		unset($query['cid']);
        	} else if ($mView == 'items' && intval($id) > 0) {
        		if (intval($cid) != intval($mCatid)) {
        			$segments[] = $cid;
        		}
        		$segments[] = $id;
        		unset($query['id']);
        		unset($query['cid']);
        	}
        }
        
        if ($view == 'items') {
        	if ($cid === null) {
        		$cid = '0:all';
        	}
            if (intval($cid) != intval($mCatid)) {
				$segments[] = $cid;
            } 
            unset($query['cid']);
            
            if ( empty($query['pid']) ||  intval($mProdid) == intval($pid)) {
            	unset($query['pid']);
            }
        }
        
        if ($view == 'producer') {
        	if (!($view == $mView && intval($pid) > 0 && intval($pid) == $mProdid) && $mView != 'producer') {
        		$segments[] = $pid;
        	}
            unset($query['pid']);
        }
    }    
    
    return $segments;
}

function DJCatalog2ParseRoute($segments) {
	
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$activemenu = $menu->getActive();
	$db = JFactory::getDBO();
	
	$catalogViews = array('item', 'items', 'producer');
	
	$query=array();
	if (count($segments)) {
		if (!in_array($segments[0], $catalogViews)) {
            if ($activemenu) {
                $temp=array();
                $temp[0] = $activemenu->query['view'];
                switch ($temp[0]) {
                	case 'item' : {
                        $temp[1] = @$activemenu->query['id'];
                        foreach ($segments as $k=>$v) {
                            $temp[$k+1] = $v;
                        }
                        break;
                    }
                    case 'items' : {
                        $temp[1] = @$activemenu->query['cid'];
                        foreach ($segments as $k=>$v) {
                            $temp[$k+1] = $v;
                        }
                        break;
                    }
                	case 'producer' : {
                        $temp[1] = @$activemenu->query['pid'];
                        foreach ($segments as $k=>$v) {
                            $temp[$k+1] = $v;
                        }
                        break;
                    }
                }
                
                $segments = $temp;
            }
        }
		if (isset($segments[0])) {
			switch($segments[0]) {
				case 'items': {
					$query['view'] = 'items';
					if (isset($segments[1])) {
						$query['cid']=($segments[1] == 'all') ? 0 : $segments[1];
					} 
					break;
				}
				case 'itemstable': {
					$query['view'] = 'itemstable';
					if (isset($segments[1])) {
						$query['cid']=($segments[1] == 'all') ? 0 : $segments[1];
					} 
					break;
				}
				case 'item': {
					$query['view'] = 'item';
					
					if (count($segments) > 2) {
						if (isset($segments[1])) {
							$query['cid']=($segments[1] == 'all') ? 0 : $segments[1];
						}
						if (isset($segments[2])) {
							$query['id']=$segments[2];
						}  
					} else if (isset($segments[1])) {
						$query['id']=$segments[1];
						if ($activemenu && $activemenu->query['option'] == 'com_djcatalog2' && $activemenu->query['view'] == 'items' && $activemenu->query['cid']) {
							$query['cid'] = $activemenu->query['cid'];
						}
					}
					break;
				}
				case 'producer': {
					$query['view'] = 'producer';
					if (isset($segments[1])) {
						$query['pid']=$segments[1];
					}  
					break;
				}
			}
		}
	}
	
	return $query;
}
