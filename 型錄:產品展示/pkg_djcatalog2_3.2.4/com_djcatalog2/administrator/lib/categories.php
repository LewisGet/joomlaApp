<?php
/**
 * @version $Id: categories.php 110 2013-01-28 10:38:36Z michal $
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
defined('_JEXEC') or die();

class Djc2CategoryNode extends JObject
{
    public $id = 0;
    public $name = 'root';
    public $catslug = '0:all';
    public $parent_id = null;
    public $published = 1;
    public $_parent = null;
    public $_children = array();

    public function __construct($category = null)
    {
    	if ($category) {
			$this->setProperties($category);
    	}
    }
    
	public function setParent(&$parent) {
    	$this->_parent = $parent;
    }
	public function addChild(&$child) {
		if (!isset($this->_children[$child->id])){
			$this->_children[$child->id] = $child;			
		}
    }
    public function getPath() {
    	if (isset($this->_path)) {
    		return $this->_path;
    	}
    	$path = new stdClass();
    	$path->items = array();
    	$slugs = array();
    	$current = $this;
    	while ($current->parent_id != null) {
    		$pathElement = new stdClass();
    		$pathElement->slug = ($current->alias) ? $current->id.':'.$current->alias : $current->id;
    		$pathElement->id = $current->id;
    		$pathElement->alias = $current->alias;
    		$slugs[] = $pathElement->slug;
    		$path->items[] = $pathElement;
    		$current = $current->_parent;
    	}
    	$this->_path = $slugs;
    	return $this->_path;
    }
    public function getChildren() {
    	return $this->_children;
    }
    public function makeChildrenList(&$list) {
    	if (count($this->_children)) {
    		foreach ($this->_children as $child ){
    			$list[] = $child->id;
    			$child->makeChildrenList($list);
    		}
    	}
    }
	public function getParent() {
    	return $this->_parent;
    }
}

class Djc2Categories
{
    protected $nodes = array();
    protected $options = array();
    static $instances = null;

    function __construct($options)
    {
    	if (!array_key_exists('order', $options) || !$options['order']) {
    		$options['order'] = 'c.ordering';
    	}
    	if (!array_key_exists('order_dir', $options) || !$options['order_dir']) {
    		$options['order_dir'] = 'ASC';	
    	}
    	
    	$this->options = $options;
    	$this->nodes[0] = new Djc2CategoryNode();
    	$this->categories = $this->getCategories($options);
    	$this->createNodes($this->categories);
    	$this->buildTree();
    }
    
    protected function createNodes($categories) {
    	foreach ($categories as $category) {
    		$this->nodes[$category->id] = new Djc2CategoryNode($category);
    	}
    }
    protected function buildTree() {
    	foreach ($this->nodes as $key=>$node) {
    		if ($node->id != 0 && isset($this->nodes[$node->parent_id])) {
    			$this->nodes[$node->id]->setParent($this->nodes[$node->parent_id]);
    		} else if ($node->id != 0) {
    			unset($this->nodes[$key]);
    			continue;
    		}
    		if (!is_null($node->parent_id) && isset($this->nodes[$node->parent_id])) {
    			$this->nodes[$node->parent_id]->addChild($node);
    		}
    	}
    }
    public function get($id) {
    	if (isset($this->nodes[$id])) {
    		return $this->nodes[$id];
    	}
    	else return false;
    }
    public static function getInstance($options = array()) {
    	
    	$hash = md5(serialize($options));
    	
		if (isset(self::$instances[$hash])) {
			return self::$instances[$hash];
		}
    	self::$instances[$hash] = new Djc2Categories($options);
    	return self::$instances[$hash];
    }
	public function getOptionList($default = null, $disableChildren = false, $selectedCategory = null, $disableDefault=false) {
    	$options = array();
    	if ($default) {
    		$options[] = JHTML::_('select.option', '0', $default,'value','text',$disableDefault);
    	}
    	foreach ($this->nodes[0]->_children as $node) {
    		$this->makeOptionList($node, $options, 0, $disableChildren, $selectedCategory);
    	}
    	return $options;
    }
    protected function makeOptionList(&$node, &$list, $level=0, $disableChildren = false, $selectedCategory = null) {
    	$prefix = '';
    	for ($i = 0; $i < $level; $i++) {
        	$prefix .= ' - ';
    	}
    	
    	$item = new stdClass();
    	$item->value = $node->id;
    	$item->text = $prefix.$node->name;
    	if ($node->id == $selectedCategory && $disableChildren) {
    		$item->disable = true;
    	} else {
    		$item->disable = null;
    	}
    	$list[] = $item;
    	foreach ($node->_children as $child) {
    		if ($item->disable) {
    			$this->makeOptionList($child, $list, $level+1, $disableChildren, $child->id);
    		} else {
    			$this->makeOptionList($child, $list, $level+1, $disableChildren, $selectedCategory);
    		}
    	}
    }
	public function getCategoryList() {
    	$categories = array();
    	foreach ($this->nodes[0]->_children as $node) {
    		$this->makeCategoryList($node, $categories, 0);
    	}
    	return $categories;
    }
    protected function makeCategoryList(&$node, &$list, $level=0) {
    	$prefix = '';
    	for ($i = 0; $i < $level; $i++) {
        	$prefix .= '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    	}
    	if ($level > 0) {
    		$prefix .= '<sup>|_</sup>&nbsp;';
    	}
    	$item = $node;
    	$item->treename = $prefix.$node->name;
    	$list[] = $item;
    	foreach ($node->_children as $child) {
    		$this->makeCategoryList($child, $list, $level+1);
    	}
    }
    
    protected function getCategories($options = array()) {
    	
    	$where = array();
    	
    	if (array_key_exists('state', $options)) {
    		if ($options['state'] == 1) {
    			$where[] = 'c.published=1';
    		}
    	}
    	$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
    	
    	$orderby = ' ORDER BY c.parent_id ASC, '.$options['order'].' '.$options['order_dir'];
    	
    	$db	= JFactory::getDbo();
		$app = JFactory::getApplication();
		$query = ' SELECT c.*, img.fullname AS item_image, img.caption AS image_caption,'
				.' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug '
				.' FROM #__djc2_categories AS c'
				. ' LEFT JOIN (select im1.fullname, im1.caption, im1.type, im1.item_id from #__djc2_images as im1, (select item_id, type, min(ordering) as lowest_order from #__djc2_images group by item_id, type) as im2 where im1.item_id = im2.item_id and im1.type=im2.type and im1.ordering = im2.lowest_order) AS img ON img.item_id = c.id AND img.type=\'category\''
				//.' LEFT JOIN #__djc2_images AS img ON img.item_id = c.id AND img.type=\'category\' AND img.ordering = 1 '
				.$where
				.$orderby;
		$db->setQuery($query);
		return $db->loadObjectList();
    } 
    
}
