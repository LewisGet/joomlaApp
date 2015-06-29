<?php
/**
* @version $Id: com_djcatalog2.php 68 2012-01-03 16:16:57Z michal $
* @package DJ-Catalog2
* @copyright Copyright (C) 2010 Blue Constant Media LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer $Author: michal $ Michal Olczyk - michal.olczyk@design-joomla.eu
*
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

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once JPATH_SITE . DS . 'components' . DS . 'com_djcatalog2' . DS . 'helpers' . DS . 'route.php';
require_once JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_djcatalog2' . DS . 'lib' . DS . 'categories.php';

class xmap_com_djcatalog2
{
    static function getTree( $xmap, $parent, &$params )
    {
        if ($xmap->isNews) // This component does not provide news content. don't waste time/resources
            return false;

        $catid=0;
        if ( strpos($parent->link, 'view=items') ) {
            $link_query = parse_url( $parent->link );
            parse_str( html_entity_decode($link_query['query']), $link_vars);
            $catid = xmap_com_djcatalog2::getParam($link_vars,'cid',0);
        }

        $include_products = xmap_com_djcatalog2::getParam($params,'include_products',1);
        $include_products = ( $include_products == 1
                                  || ( $include_products == 2 && $xmap->view == 'xml') 
                                  || ( $include_products == 3 && $xmap->view == 'html')
                                  ||   $xmap->view == 'navigator');
        $params['include_products'] = $include_products;

        $priority = xmap_com_djcatalog2::getParam($params,'cat_priority',$parent->priority);
        $changefreq = xmap_com_djcatalog2::getParam($params,'cat_changefreq',$parent->changefreq);
        if ($priority  == '-1')
            $priority = $parent->priority;
        if ($changefreq  == '-1')
            $changefreq = $parent->changefreq;

        $params['cat_priority'] = $priority;
        $params['cat_changefreq'] = $changefreq;

        $priority = xmap_com_djcatalog2::getParam($params,'link_priority',$parent->priority);
        $changefreq = xmap_com_djcatalog2::getParam($params,'link_changefreq',$parent->changefreq);
        if ($priority  == '-1')
            $priority = $parent->priority;

        if ($changefreq  == '-1')
            $changefreq = $parent->changefreq;

        $params['link_priority'] = $priority;
        $params['link_changefreq'] = $changefreq;

        xmap_com_djcatalog2::getDJCatalog2Category($xmap,$parent,$params,$catid);
    }

    /* Returns URLs of all Categories and links in of one category using recursion */
    static function getDJCatalog2Category (&$xmap, &$parent, &$params, &$catid )
    {
        $database = JFactory::getDBO();
        
		$djc2categories = Djc2Categories::getInstance(array('state'=>'1'));
        $category = $djc2categories->get($catid);
		$categories = $category->getChildren();
        $xmap->changeLevel(1);
        foreach($categories as $row) {
            if( !$row->created ) {
                $row->created = $xmap->now;
            }

            $node = new stdclass;
            $node->name = $row->name;
            $node->link = DJCatalogHelperRoute::getCategoryRoute($row->id);
            $node->id = $parent->id;
            $node->uid = $parent->uid .'c'.$row->id;
            $node->browserNav = $parent->browserNav;
            $node->modified = $row->created;
            $node->priority = $params['cat_priority'];
            $node->changefreq = $params['cat_changefreq'];
            $node->expandible = true;
            $node->secure = $parent->secure;

            if ( $xmap->printNode($node) !== FALSE) {
                xmap_com_djcatalog2::getDJCatalog2Category($xmap,$parent,$params,$row->id);
            }
        }

        /* Returns URLs of all listings in the current category */
        if ($params['include_products']) {
            $query = " SELECT a.name, a.id, a.cat_id, UNIX_TIMESTAMP(a.created) as `created` \n".
                 " FROM #__djc2_items AS a \n".
                 " WHERE a.cat_id = ".(int)$catid.
                 " AND a.published=1" .
                 " ORDER BY a.ordering ASC, a.name ASC ";

            $database->setQuery($query);

            $rows = $database->loadObjectList();

            foreach($rows as $row) {
                $node = new stdclass;
                $node->name = $row->name;
                $node->link = DJCatalogHelperRoute::getItemRoute($row->id, $row->cat_id);
                $node->id = $parent->id;
                $node->uid = $parent->uid.'i'.$row->id;
                $node->browserNav = $parent->browserNav;
                $node->modified = ($row->created);
                $node->priority = $params['link_priority'];
                $node->changefreq = $params['link_changefreq'];
                $node->expandible = false;
                $node->secure = $parent->secure;
                $xmap->printNode($node);
            }
        }
        $xmap->changeLevel(-1);
        
    }

    static function getParam($arr, $name, $def)
    {
        $var = JArrayHelper::getValue( $arr, $name, $def, '' );
        return $var;
    }
}
