<?php
/**
 * @version $Id: items.php 114 2013-02-01 10:04:49Z michal $
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

jimport('joomla.application.component.modellist');

class DJCatalog2ModelItems extends JModelList {
	var $_list = null;
	var $_pagination = null;
	var $_total = null;
	var $_producers = null;
	var $_params = null;
	var $_attributes = null;
		
	function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		$params = Djcatalog2Helper::getParams();
		$this->setState('params', $params);

		$filter_featured	= $params->get('featured_only', 0);
		$this->setState('filter.featured', $filter_featured);
		
		$filter_catid		= (int) JRequest::getVar( 'cid',0,'default','string' );
		$this->setState('filter.category', $filter_catid);
		
		$filter_catalogue = $params->get('product_catalogue', false) == true ? true : false;
		$this->setState('filter.catalogue', $filter_catalogue);
		
		$filter_producerid 	= (int) JRequest::getVar( 'pid',0,'default','string' );
		$this->setState('filter.producer', $filter_producerid);
		
		$filter_index       =  JRequest::getVar( 'ind',null,'default','string' );
		$this->setState('filter.index', $filter_index);
		
		$filters 			= JRequest::getVar('djcf',array(),'default','array');
		
		$request = $_REQUEST;
		foreach($request as $param=>$value) {
			if (!array_key_exists('djcf', $request)) {
				$request['djcf'] = array();
			}
			if (strstr($param, 'f_')) {
				$qkey = substr($param, 2);
				$qval = (strstr($value,',') !== false) ? explode(',',$value) : $value;
				unset($request[$param]);
				$request['djcf'][$qkey] = $qval;
			}
		}
		$filters = $request['djcf'];
		
		$this->setState('filter.customattribute', $filters);
		
		$searches 			= JRequest::getVar('djcs',array(),'default','array');
		$this->setState('filter.customsearch', $searches);
		
		$globalSearch 		= urldecode(JRequest::getVar( 'search','','default','string' ));
		$this->setState('filter.search', $globalSearch);
		
		$order		= JRequest::getVar( 'order', $params->get('items_default_order','i.ordering'),'default','cmd' );
		$this->setState('list.ordering', $order);
		
		$order_dir	= JRequest::getVar( 'dir',	$params->get('items_default_order_dir','asc'),'default','word' );
		$this->setState('list.direction', $order_dir);
		
		$order_featured	= $params->get('featured_first', 0);
		$this->setState('list.ordering_featured', $order_featured);
		
		$limit		= JRequest::getVar( 'limit', $params->get('limit_items_show',10), 'default', 'int' );
		$this->setState('list.limit', $limit);
		
		$limitstart	= JRequest::getVar( 'limitstart', 0, 'default', 'int' );
		$this->setState('list.start', $limitstart);
		
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.featured');
		$id	.= ':'.$this->getState('filter.category');
		$id	.= ':'.$this->getState('filter.catalogue');
		$id	.= ':'.$this->getState('filter.producer');
		$id	.= ':'.$this->getState('filter.index');
		$id	.= ':'.serialize($this->getState('filter.customattribute'));
		$id	.= ':'.serialize($this->getState('filter.customsearch'));
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');
		$id	.= ':'.$this->getState('list.ordering_featured');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.start');

		return md5($this->context . ':' . $id);
	}
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$this->_db->setQuery($query, $limitstart, $limit);
		$result = $this->_db->loadObjectList('id');

		return $result;
	}
	
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}
		// Load the list items.
		$query = $this->_getListQuery();
		$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;
		
		$this->bindAttributes($store);
		
		return $this->cache[$store];
	}
	protected function getListQuery()
	{
		return $this->_buildQuery();
	}
	
	function _buildQuery($ignoreFilters = array()) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$where		= $this->_buildContentWhere($ignoreFilters);
		$orderby	= $this->_buildContentOrderBy();
		$attributes = $this->getAttributes(true);
		$textSearch = array();
		
		$filters = $this->getState('filter.customattribute');
		$searches = $this->getState('filter.customsearch');
		$globalSearch = $this->getState('filter.search');
		
		$query->select('i.*');
		$query->select('CASE WHEN CHAR_LENGTH(i.alias) THEN CONCAT_WS(":", i.id, i.alias) ELSE i.id END as slug ');
		$query->from('#__djc2_items as i');
		
		$query->select('c.id as _category_id, c.name as category, c.published as publish_category');
		$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug ');
		$query->join('left','#__djc2_categories AS c ON c.id = i.cat_id');
		
		$query->select('p.id as _producer_id, p.name as producer, p.published as publish_producer');
		$query->select('CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(":", p.id, p.alias) ELSE p.id END as prodslug ');
		$query->join('left','#__djc2_producers AS p ON p.id = i.producer_id');
		
		$query->select('img.fullname as item_image, img.caption AS image_caption');
		$query->join('left', '(select im1.fullname, im1.caption, im1.type, im1.item_id from #__djc2_images as im1, (select item_id, type, min(ordering) as lowest_order from #__djc2_images group by item_id, type) as im2 where im1.item_id = im2.item_id and im1.type=im2.type and im1.ordering = im2.lowest_order) AS img ON img.item_id = i.id AND img.type=\'item\'');
		
		$query->select('group_concat(distinct ic.category_id order by ic.category_id asc separator \'|\') AS categorylist');
		$query->join('left', '#__djc2_items_categories AS ic ON ic.item_id=i.id');
		
		$globalSearch = trim(JString::strtolower( $globalSearch ));
		if (substr($globalSearch,0,1) == '"' && substr($globalSearch, -1) == '"') { 
			$globalSearch = substr($globalSearch,1,-1);
		}
		if (strlen($globalSearch) > 0 && (strlen($globalSearch)) < 3 || strlen($globalSearch) > 20) {
			 $globalSearch = null;
		}
		
		$doTextSearch = !in_array('search', $ignoreFilters);
		if ($doTextSearch && $globalSearch) {
			$textSearch[] = 'LOWER(i.name) LIKE '.$db->Quote( '%'.$db->escape( $globalSearch, true ).'%', false );
			$textSearch[] = 'LOWER(i.description) LIKE '.$db->Quote( '%'.$db->escape( $globalSearch, true ).'%', false );
			$textSearch[] = 'LOWER(i.intro_desc) LIKE '.$db->Quote( '%'.$db->escape( $globalSearch, true ).'%', false );
			$textSearch[] = 'LOWER(c.name) LIKE '.$db->Quote( '%'.$db->escape( $globalSearch, true ).'%', false );
			$textSearch[] = 'LOWER(p.name) LIKE '.$db->Quote( '%'.$db->escape( $globalSearch, true ).'%', false );
			
			$optionsSearch = 
			     ' select i.id '
				.' from #__djc2_items as i '
				.' inner join #__djc2_items_extra_fields_values_int as efv on efv.item_id = i.id'
				.' inner join #__djc2_items_extra_fields as ef on ef.id = efv.field_id and ef.searchable = 1 '
				.' inner join #__djc2_items_extra_fields_options as efo on efo.id = efv.value and lower(efo.value) like '.$db->Quote( '%'.$db->escape( $globalSearch, true ).'%', false )
				.' union '
				. 'select i.id '
				.' from #__djc2_items as i '
				.' inner join #__djc2_items_extra_fields_values_text as efv on efv.item_id = i.id'
				.' inner join #__djc2_items_extra_fields as ef on ef.id = efv.field_id and ef.searchable = 1 and lower(efv.value) like '.$db->Quote( '%'.$db->escape( $globalSearch, true ).'%', false )
				.' group by i.id '
				;
			$textSearch[] = 'i.id IN ('.$optionsSearch.')';
		}
		
		
		$doCustomSearch = !in_array('custom_fields', $ignoreFilters);
		
		if ($doCustomSearch) {
			$filter_unions = array();
			foreach ($attributes as $key=>$attribute) {
				$attributes[$key]->alias = str_replace('-', '_', $attribute->alias);
				
				if (!empty($filters[$attribute->alias])) {
					$filter = $filters[$attribute->alias];
					if ($attribute->filterable == 1) {
						
						if (is_scalar($filter) && strpos($filter, ',') !== false) {
							$filter = explode(',', $filter);
						}
						
						if (is_array($filter)) {
							foreach($filter as $key=>$opt) {
								if (is_scalar($opt)) {
									$filter_unions[] = '(select * from #__djc2_items_extra_fields_values_int where field_id='.$attribute->id.' and value='.(int)$opt.')';
								}
							}
						} else {
							$filter_unions[] = '(select * from #__djc2_items_extra_fields_values_int where field_id='.$attribute->id.' and value='.(int)$filter.')';
						}
					}
				}
				
			}
			
			if (count($filter_unions) > 0) {
				$unionQuery = 'select * from (select count(*) as c, item_id from ('.implode(' union ', $filter_unions).') as f group by f.item_id) as filter_counter where filter_counter.c='.count($filter_unions);
				$query->join('inner', '('.$unionQuery.') as filters on filters.item_id = i.id');
			}
		}
		
		if ($doTextSearch && count($textSearch)) {
			$where[] = ' ( '.implode( ' OR ', $textSearch ).' ) ';
		}
		
		$query->where($where);
		$query->group('i.id');
		$query->order($orderby);
		//echo str_replace('#_','jos',$query).'<br/>';die();
		return $query;
	}


	function _buildContentOrderBy()
	{
		$filter_order		= $this->getState('list.ordering');
		$filter_order_Dir	= $this->getState('list.direction');
		$filter_featured	= $this->getState('list.ordering_featured');
		
		$sortables = array('i.ordering', 'i.name', 'i.created', 'i.price', 'category', 'c.name', 'producer', 'p.name', 'i.id', 'rand()');
		
		if (!in_array($filter_order, $sortables)) {
			$filter_order = 'i.ordering';
		}
		
		if ($filter_order_Dir != 'asc' && $filter_order_Dir != 'desc') {
			$filter_order_Dir = 'asc';
		}
		
		if ($filter_order == 'i.ordering'){
			if ($filter_featured) {
				$orderby 	= ' i.featured DESC, i.ordering '.$filter_order_Dir.', c.ordering '.$filter_order_Dir;
			} else {
				$orderby 	= ' i.ordering '.$filter_order_Dir.', c.ordering '.$filter_order_Dir;
			}
		} else {
			// older version compatibility
			switch ($filter_order) {
				case 'producer': {
					$filter_order = 'p.name';
					break;
				}
				case 'category': {
					$filter_order = 'c.name';
					break;
				}
			}
			if ($filter_featured) {
				$orderby 	= ' i.featured DESC, '.$filter_order.' '.$filter_order_Dir.' , i.ordering, c.ordering ';
			}
			else {
				$orderby 	= ' '.$filter_order.' '.$filter_order_Dir.' , i.ordering, c.ordering ';
			}
		}
		return $orderby;
	}

	function _buildContentWhere($ignoreFilters = array())
	{
		$view = JRequest::getVar('view');
		$db					= JFactory::getDBO();
		
		$params = $this->getState('params');
		
		$filter_featured	= $this->getState('filter.featured');
		
		$filter_catid		= $this->getState('filter.category');
		$filter_catalogue		= $this->getState('filter.catalogue');
		$filter_producerid  = $this->getState('filter.producer');
		
		$filter_index       =  $this->getState('filter.index');

		$where = array();
		
		if (is_array($filter_catid) && !empty($filter_catid)) {
			JArrayHelper::toInteger($filter_catid);
			$db->setQuery('SELECT item_id 
						   FROM #__djc2_items_categories AS ic
						   INNER JOIN #__djc2_categories AS c ON c.id=ic.category_id 
						   WHERE category_id IN ('.implode(',',$filter_catid).') AND c.published = 1');
			$items = $db->loadColumn();
			
			if (count ($items)) {
				$items = array_unique($items);
				$where[] = 'i.id IN ('.implode(',',$items).')';
			} else {
				$where[] = '1 = 0';
			}
		}
		else if ((int)$filter_catid >= 0) {
			
			if ($filter_catalogue && is_scalar($filter_catid)) {
				//$where[] = 'i.cat_id = '.(int) $filter_catid;
				$db->setQuery('SELECT item_id 
					   FROM #__djc2_items_categories AS ic
					   INNER JOIN #__djc2_categories AS c ON c.id=ic.category_id 
					   WHERE category_id=\''.(int)$filter_catid.'\' AND c.published = 1');
				$items = $db->loadColumn();
				if (count ($items) > 0) {
					$items = array_unique($items);
					$where[] = 'i.id IN ('.implode(',',$items).')';
				} else $where[] ='1 = 0';
			}
			else {
				$categories = Djc2Categories::getInstance(array('state'=>'1'));
				if ($parent = $categories->get((int)$filter_catid) ) {
					$childrenList = array($parent->id);
					$parent->makeChildrenList($childrenList);
					if ($childrenList) {
						$cids = implode(',', $childrenList);
						$db->setQuery('SELECT item_id 
									   FROM #__djc2_items_categories AS ic
									   INNER JOIN #__djc2_categories AS c ON c.id=ic.category_id 
									   WHERE category_id IN ('.$cids.') AND c.published = 1');
						$items = $db->loadColumn();
						if (count ($items)) {
							$items = array_unique($items);
							$where[] = 'i.id IN ('.implode(',',$items).')';
						} else {
							$where[] = '1 = 0';
						}
						//$where[] = 'i.cat_id IN ( '.$cids.' )';
					}
					else if ($filter_catid != 0){
						JError::raiseError( 404, JText::_("COM_DJCATALOG2_PAGE_NOT_FOUND") );
					}
				}
			}
			
		}
		
		if (!in_array('producer', $ignoreFilters) && $filter_producerid > 0) {
            $where[] = 'i.producer_id = '.(int) $filter_producerid;
        }
		
		if (!in_array('featured', $ignoreFilters) && $filter_featured > 0) {
			$where[] = 'i.featured = 1';
		}
		
		if (!in_array('atoz', $ignoreFilters) && $filter_index) {
            $where[] = ' LOWER(i.name) LIKE '.$db->Quote( $db->escape( $filter_index, true ).'%', false );
        }
        
		$where[] = 'i.published = 1';
		return $where;
	}
	
	function getAttributes($all = false) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('f.*, group_concat(fo.id order by fo.ordering asc separator \'|\') as options');
			$query->from('#__djc2_items_extra_fields as f');
			$query->join('LEFT', '#__djc2_items_extra_fields_options as fo ON fo.field_id=f.id');
			
			if ($all) {
				$query->where('f.published = 1');
			} else {
				$query->where('(f.visibility = 2 or f.visibility = 3) and f.published = 1');
			}
			$query->group('f.id');
			$query->order('f.ordering asc, fo.ordering asc');
			$db->setQuery($query);
			$this->_attributes = $db->loadObjectList();
		return $this->_attributes;
	}

	function bindAttributes($store) {
		if (!empty($this->cache[$store])) {
			$ids = array_keys($this->cache[$store]);
			$db = JFactory::getDbo();
			
			$query_int = $db->getQuery(true);
			$query_text = $db->getQuery(true);
			
			$query_int->select('fields.alias, fields.type, fields.ordering, fieldvalues.item_id, fieldvalues.field_id, fieldvalues.id as value_id, fieldoptions.id as option_id, fieldoptions.value');
			$query_int->from('#__djc2_items_extra_fields_values_int as fieldvalues');
			$query_int->join('inner', '#__djc2_items as items on items.id=fieldvalues.item_id' );
			$query_int->join('inner','#__djc2_items_extra_fields as fields ON fields.id = fieldvalues.field_id');
			$query_int->join('left','#__djc2_items_extra_fields_options as fieldoptions ON fieldoptions.id = fieldvalues.value AND fieldoptions.field_id = fields.id');
			$query_int->where('fieldvalues.item_id IN ('.implode(',',$ids).') AND (fields.visibility = 2 OR fields.visibility = 3) AND fields.published = 1');
			$query_int->order('fieldvalues.field_id asc, fieldvalues.field_id asc');
			
			$query_text->select('fields.alias, fields.type, fields.ordering, fieldvalues.item_id, fieldvalues.field_id, fieldvalues.id as value_id, 0 as option_id, fieldvalues.value');
			$query_text->from('#__djc2_items_extra_fields_values_text as fieldvalues');
			$query_text->join('inner', '#__djc2_items as items on items.id=fieldvalues.item_id' );
			$query_text->join('inner','#__djc2_items_extra_fields as fields ON fields.id = fieldvalues.field_id');
			$query_text->where('fieldvalues.item_id IN ('.implode(',',$ids).') AND (fields.visibility = 2 OR fields.visibility = 3) AND fields.published = 1');
			$query_text->order('fieldvalues.field_id asc, fieldvalues.field_id asc');
			
			//$query = 'SELECT * FROM (('.(string)$query_int.') UNION DISTINCT ('.(string)$query_text.')) as list ORDER BY list.field_id asc, list.item_id asc';
			//echo str_replace('#_','jos',$query);die();
			
			// I decided not to use UNION because of FaLang translation issues
			
			$db->setQuery($query_int);
			$int_attributes = $db->loadObjectList();
			
			$db->setQuery($query_text);
			$text_attributes = $db->loadObjectList();
			
			
			foreach ($text_attributes as $attribute) {
				$field = $attribute->alias;
				$this->cache[$store][$attribute->item_id]->$field = $attribute->value;
				//$this->cache[$store][$attribute->item_id]->$field = $attribute->optionvalues ? $attribute->optionvalues : $attribute->value;
			}
			foreach ($int_attributes as $attribute) {
				$field = $attribute->alias;
				if (!isset($this->cache[$store][$attribute->item_id]->$field) || !is_array($this->cache[$store][$attribute->item_id]->$field)) {
					$this->cache[$store][$attribute->item_id]->$field = array();
				}
				$tmp_arr = $this->cache[$store][$attribute->item_id]->$field;
				$tmp_arr[] = $attribute->value;
				$this->cache[$store][$attribute->item_id]->$field = $tmp_arr;
			}
		}
	}
	
	function getProducers(){
		if(!$this->_producers) {
			$db = JFactory::getDbo();
			$filter_catid		= $this->getState('filter.category');
			$filter_producerid    = $this->getState('filter.producer');
			
			$query = null;
			if ($filter_catid > 0) {
				$categories = Djc2Categories::getInstance(array('state'=>'1'));
				if ($parent = $categories->get((int)$filter_catid) ) {
					$childrenList = array($parent->id);
					$parent->makeChildrenList($childrenList);
					$query = 'SELECT DISTINCT p.id, p.name as text, '
						. ' CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(":", p.id, p.alias) ELSE p.id END as value '
						.' FROM #__djc2_producers as p '
						.' INNER JOIN #__djc2_items AS i ON p.id = i.producer_id '
	            		.' INNER JOIN #__djc2_categories AS c ON c.id = i.cat_id '
						.' WHERE c.id IN ('.implode(',', $childrenList).') AND p.published=1 ORDER BY text';
				}
			} else {
				$query = 'SELECT p.id, p.name as text, '
					. ' CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(":", p.id, p.alias) ELSE p.id END as value '
					.' FROM #__djc2_producers as p WHERE p.published=1 ORDER BY text';
			}
			$db->setQuery($query);
			$items = $db->loadObjectList();
			$this->_producers = $db->loadObjectList();
		}
		return $this->_producers;
	}	
	
	function getParams() {
		return Djcatalog2Helper::getParams();
	}
    
    function getSubCategories($category) {
        
        $db = JFactory::getDbo();
        $parent_id = $category->id;
        $db->setQuery('
                select ic.category_id as category_id, count(i.id) as item_count
                from #__djc2_items_categories as ic
                left join #__djc2_items as i on i.id = ic.item_id 
                inner join #__djc2_categories as c on c.id = ic.category_id 
                where i.published = 1
                group by ic.category_id
                order by c.parent_id, c.ordering asc, c.name asc
            ');   
        
        $categoryList = $db->loadObjectList('category_id');

        $children = $category->getChildren();
        
        foreach ($children as $k=>$v) {
            $this->countChildren($v, $categoryList);
        }
        
        
        $subcategories = array();
        foreach ($children as $subcategory) {
            if (array_key_exists($subcategory->id, $categoryList)) {
                $subcategories[] = $subcategory;
            }
        }
        return $subcategories;
    }
    
    protected function countChildren(&$node, &$countList) {
        $children = $node->getChildren();
        $node->item_count = (isset($countList[$node->id])) ? $countList[$node->id]->item_count : 0;
        if (count($children)) {
            foreach ($children as $child) {
                $node->item_count += $this->countChildren($child, $countList);
            }
        }
        
        return $node->item_count;
    }
    protected function makeCategoryTree( $id, $list, &$children, $level=0) {
        if (array_key_exists($id, $children)) {
            foreach ($children[$id] as $child)
            {
                $id = $child->id;

                $pt = $child->parent_id;
                $list[$id] = $child;
                if (array_key_exists($id, $children)) {
                    $list[$id]->children = count( $children[$id] );
                }
                else {
                    $list[$id]->children = 0;
                }
                $list[$id]->level = $level;
                $list = $this->makeCategoryTree( $id, $list, $children, $level+1);
            }
                
        }
        return $list;
    }
    
	public function getIndexCount() {
        $db = JFactory::getDBO();
        
        $query = $this->_buildQuery(array('atoz'));
		$db->setQuery($query);
		$matchingitems = $db->loadObjectList('id');
		
		if (count($matchingitems) > 0) {
			$itemIds = implode(',', array_keys($matchingitems));
			//$letters = array('a','b','c','ć','d','e','f','g','h','i','j','k','l','ł','m','n','ń','o','ó','p','q','r','s','ś','t','u','v','w','x','y','z','ź','ż');
	        $letters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
	        $select = $join = array();
	        foreach ($letters as $letter) {
	            $select[] = ' count('.$letter.'.id) as '.$letter;
	            $join[] = 'left join #__djc2_items as '.$letter.' on '.$letter.'.id = items.id and lower('.$letter.'.name) like \''.$letter.'%\'';
	        }
	        
	        $query = '';
	        $query .= 'SELECT '.implode(', ',$select).PHP_EOL.' FROM #__djc2_items as items '.PHP_EOL.implode(PHP_EOL,$join).' where items.id in ('.$itemIds.')';
	        
	        $db->setQuery($query);
	        $items = $db->loadObject();
	        return $items;
		}
		return null;        
    }
	
}

