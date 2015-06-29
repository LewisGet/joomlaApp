<?php
/**
 * @version $Id: modfrontpage.php 110 2013-01-28 10:38:36Z michal $
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

jimport('joomla.application.component.model');

class DJCatalog2ModelModfrontpage extends JModelLegacy {
	var $_list = null;
	var $_pagination = null;
	var $_total = null;
	var $_params = null;
	
	function getXml() {
		$moduleclass_sfx = JRequest::getVar( 'moduleclass_sfx', null, 'default', 'string');
		$mid = (int)JRequest::getVar( 'moduleId', null, 'default', 'int');
		$stitle = JRequest::getVar( 'stitle', '1', 'default', 'string');
		$ltitle = JRequest::getVar( 'ltitle', '1', 'default', 'string');
		$scattitle = JRequest::getVar( 'scattitle', null, 'default', 'string');
		$spag = JRequest::getVar( 'spag', null, 'default', 'int');
		//$orderby = JRequest::getVar( 'orderby', null, 'get', 'int', 0);
		//$orderdir = JRequest::getVar( 'orderdir', 0, 'get', 'int', 0);
		//$featured_only = JRequest::getVar( 'featured_only', 0, 'get', 'int', 0);
		//$featured_first = JRequest::getVar( 'featured_first', 0, 'get', 'int', 0);
		$cols = (int)JRequest::getVar( 'cols', null, 'default', 'int');
		$rows = (int)JRequest::getVar( 'rows', null, 'default', 'int');
		//$mainimage = JRequest::getVar( 'mainimg', 'large', 'get', 'string', 0);
		$trunc = (int)JRequest::getVar( 'trunc', '0', 'default', 'int');
		$trunclimit = (int)JRequest::getVar( 'trunclimit', '0', 'default', 'int');
		
		$showreadmore = (int)JRequest::getVar( 'showreadmore', '1', 'default', 'int');
		$readmoretext = JRequest::getVar( 'readmoretext', '', 'post');
		$readmoretext = ($readmoretext != '') ? urldecode($readmoretext) : JText::_('COM_DJCATALOG2_READMORE');
		
		$paginationStart = (int)JRequest::getVar( 'pagstart', null, 'default', 'int');
		$categories = JRequest::getVar( 'categories', null, 'default', 'string');
		$catsw = JRequest::getVar( 'catsw', 0, 'default', 'int');
		$categories = explode('|', $categories);
		
		$largewidth = JRequest::getVar('largewidth','400','default','int');
		$largeheight = JRequest::getVar('largeheight','240','default','int');
		$largecrop = JRequest::getVar('largecrop',1,'default','int') ? true:false;
		$smallwidth = JRequest::getVar('smallwidth','90','default','int');
		$smallheight = JRequest::getVar('smallheight','70','default','int');
		$smallcrop = JRequest::getVar('smallcrop',1,'default','int') ? true:false;
		
		$Itemid = JRequest::getVar('Itemid',0, 'default', 'int');
		$path = JURI::base();
		
		$itemsCount = $this->getTotal();
		$itemsPerPage = $rows * $cols;
		$paginationBar = null;
		//if ($spag == 1) {
			$paginationBar ='<pagination><![CDATA[';
			if ($itemsCount > $itemsPerPage && $itemsPerPage > 0) {
				if ((int)$spag == 1) {
					for ($i = 0; $i < $itemsCount; $i = $i + $itemsPerPage) {
						$counter = (int)(($i+$itemsPerPage)/$itemsPerPage);
						if ($paginationStart == $i) $active=' active';
						else $active='';
						$paginationBar .= '<span class="btn'.$active.' button" style="cursor: pointer;" onclick="DJFrontpage_'.$mid.'.loadPage('.$i.'); return false;">'.$counter.'</span>&nbsp;';
					}
				} else {
					$prevPage = $paginationStart - $itemsPerPage;
					$nextPage = $paginationStart + $itemsPerPage;
					$firstPage = 0;
					$lastPage = $itemsPerPage * (ceil($itemsCount / $itemsPerPage) - 1);
					if ($paginationStart == 0) {
						$prevPage = $lastPage;
					}
					if ($paginationStart == $lastPage) {
						$nextPage = $firstPage;
					}
					
					$paginationBar .= '<span class="djcf_prev_button" style="cursor: pointer;" onclick="DJFrontpage_'.$mid.'.loadPage('.$prevPage.'); return false;"></span>&nbsp;';
					$paginationBar .= '<span class="djcf_next_button" style="cursor: pointer;" onclick="DJFrontpage_'.$mid.'.loadPage('.$nextPage.'); return false;"></span>';
				}				
			}
			$paginationBar .= ']]></pagination>';
		//}
		
		$items = $this->getList($paginationStart, $itemsPerPage);

		$output = '<?xml version="1.0" encoding="utf-8" ?><contents>';
		$gallery ='';
		for ($i = 0; $i  < count($items); $i++) {
			$title = '';
			$readmore = JRoute::_(DJCatalogHelperRoute::getItemRoute($items[$i]->slug, $items[$i]->catslug));
			if($stitle == '1') {
				if ($ltitle) {
					$title='<h3><a href="'.$readmore.'">'.$items[$i]->name.'</a></h3>';
				} else {
					$title='<h3>'.$items[$i]->name.'</h3>';
				}
			}
	
			$cattitle = '';
			if($scattitle == 1) 
				{
					$cattitle='<h2>'.$items[$i]->category.'</h2>';
				}
			
			if ($trunc > 0 && $trunclimit >0) {
				$items[$i]->intro_desc = DJCatalog2HtmlHelper::trimText($items[$i]->intro_desc, $trunclimit);
			}
			
			$output .= '<content>';
			if($scattitle == 1)
				$output .= '<category><![CDATA['.$cattitle.']]></category>';
			$output .= '<text><![CDATA['.$title.'<div class="djf_desc">'.$items[$i]->intro_desc.'</div>';
			
			if ($showreadmore == '1') {
				$output	.='<a class="btn btn-primary btn-large" href="'.$readmore.'">'.$readmoretext.'</a>';
			}
			$output	.= ']]></text>';
			$output .= '<image><![CDATA['.DJCatalog2ImageHelper::getProcessedImage($items[$i]->item_image, $largewidth, $largeheight, !$largecrop).']]></image>';
			$output .= '<src><![CDATA['.DJCatalog2ImageHelper::getImageUrl($items[$i]->item_image,'fullscreen').']]></src>';
			$output .= '</content>';
			if ($items[$i]->item_image) {
				$gallery .=	'<thumb><![CDATA[<div class="djf_cell img-polaroid"><a href="'.$readmore.'" onclick="DJFrontpage_'.$mid.'.loadItem('.$i.'); return false;"><img src="'.DJCatalog2ImageHelper::getProcessedImage($items[$i]->item_image, $smallwidth, $smallheight, !$smallcrop).'" alt="'.$items[$i]->image_caption.'" /></a></div>]]></thumb>';				
			} else {
				$gallery .=	'<thumb><![CDATA[]]></thumb>';
			}
		}
		
		$all = $output.$gallery.$paginationBar;
		$all .= '</contents>';
		return $all;
	}
	function getList($start, $limit)
	{
		if (empty($this->_list))
		{
			$query = $this->_buildQuery();
			$this->_list = $this->_getList($query, $start, $limit);
		}

		return $this->_list;
	}

	function getTotal()
	{
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	function getPagination($start, $limit)
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $start, $limit );
		}

		return $this->_pagination;
	}

	function _buildQuery()
	{
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = ' SELECT i.*, c.id AS ccategory_id, p.id AS pproducer_id, c.name AS category, p.name AS producer, p.published as publish_producer, img.fullname AS item_image, img.caption AS image_caption, '
			. ' CASE WHEN CHAR_LENGTH(i.alias) THEN CONCAT_WS(":", i.id, i.alias) ELSE i.id END as slug, '
			. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug, '
			. ' CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(":", p.id, p.alias) ELSE p.id END as prodslug '
			. ' FROM #__djc2_items AS i '
			. ' LEFT JOIN #__djc2_categories AS c ON c.id = i.cat_id '
			. ' LEFT JOIN #__djc2_producers AS p ON p.id = i.producer_id '
			//. ' LEFT JOIN #__djc2_images AS img ON img.item_id = i.id AND img.type=\'item\' AND img.ordering = 1 '
			. ' INNER JOIN (SELECT im1.* from #__djc2_images as im1 GROUP BY im1.item_id, im1.type ORDER BY im1.ordering asc) AS img ON img.item_id = i.id AND img.type=\'item\' '
			. $where
			. $orderby
		;
		//echo str_replace('#_', 'jos', $query);
		return $query;
	}

	function _buildContentOrderBy()
	{
		$filter_order		= JRequest::getVar( 'order',null,'default','cmd' );
		$filter_order_Dir	= JRequest::getVar( 'dir',	0,'asc','default','word' );
		
		$featured_first = JRequest::getVar( 'featured_first', 0, 'default', 'int', 0);
		$featured_order = '';
		if ($featured_first) {
			$featured_order = ' i.featured DESC, ';
		}
		
		$orderby = JRequest::getVar( 'orderby', null, 'default', 'int', 0);
		$orderdir = JRequest::getVar( 'orderdir', 0, 'default', 'int', 0);
		
		$orderbydir = ($orderdir == 0) ? ' ASC':' DESC'; 
		$featured_order = '';
		if ($featured_first) {
			$featured_order = ' i.featured DESC, ';
		}
		
		switch ($orderby) {
			case '0':
				$orderbyQuery = ' ORDER BY '.$featured_order.' i.ordering'.$orderbydir.', i.name';
				break;
			case '1':
				$orderbyQuery = ' ORDER BY '.$featured_order.' i.name'.$orderbydir.', i.ordering';
				break;
			case '2':
				$orderbyQuery = ' ORDER BY '.$featured_order.' c.ordering'.$orderbydir.', i.ordering';
				break;
			case '3':
				$orderbyQuery = ' ORDER BY '.$featured_order.' p.ordering'.$orderbydir.', i.ordering';
				break;
			case '4':
				$orderbyQuery = ' ORDER BY '.$featured_order.' i.price'.$orderbydir.', i.ordering';
				break;
			case '5':
				$orderbyQuery = ' ORDER BY '.$featured_order.' i.id'.$orderbydir.', i.ordering';
				break;
			case '6':
				$orderbyQuery = ' ORDER BY '.$featured_order.' i.created'.$orderbydir.', i.ordering';
				break;
			default:
				$orderbyQuery = ' ORDER BY '.$featured_order.' i.ordering'.$orderbydir.', i.name';
				break;
		}
		
		return $orderbyQuery;
	}

	function _buildContentWhere()
	{
		$view = JRequest::getVar('view');
		$db					= JFactory::getDBO();
		
		$featured_only = JRequest::getVar( 'featured_only', 0, 'default', 'int', 0);
		$categories = JRequest::getVar( 'categories', null, 'default', 'string', 0);
		$catsw = JRequest::getVar( 'catsw', 0, 'default', 'int', 0);
		$categories = explode('|', $categories);
		
		$where = array();
		if ($catsw && count($categories) > 0) {
			$categories = array_unique($categories);
			$catlist = implode(',',$categories);
			$db->setQuery('SELECT item_id 
						   FROM #__djc2_items_categories AS ic
						   INNER JOIN #__djc2_categories AS c ON c.id=ic.category_id 
						   WHERE category_id IN ('.$catlist.') AND c.published = 1');
			$items = $db->loadColumn();
			if (count ($items) > 0) {
				$items = array_unique($items);
				$where[] = 'i.id IN ('.implode(',',$items).')';
			} else $where[] = '1=0';
		}
		
		if ($featured_only > 0) {
			$where[] = 'i.featured = 1';
		}
		$where[] = 'i.published = 1';
		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	
}

