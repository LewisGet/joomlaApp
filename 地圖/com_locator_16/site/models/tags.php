<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: location.php 307 2010-01-29 17:10:42Z fatica $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Directory Component Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class LocatorModelTags extends JModel
{

	/**
	 * Directory data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Directory total
	 *
	 * @var integer
	 */
	var $_total = null;

	
	function save(){

		$sql = "";
		//die(print_r($_REQUEST));
		$id = (int)JRequest::getInt('id');
		
		$order = (int)JRequest::getInt('order',0);

		$name = addslashes(JRequest::getString('name'));	
		$description = addslashes(JRequest::getString('description'));
		$marker = addslashes(JRequest::getString('marker'));
		$user_tag = addslashes(JRequest::getInt('user_tag'));
		$marker_shadow = addslashes(JRequest::getString('marker_shadow'));
		$tag_group = addslashes(JRequest::getString('tag_group'));
		$child_of= addslashes(JRequest::getString('child_of'));
		$tag_group_order= JRequest::getInt('tag_group_order');
		
		$db = JFactory::getDBO();
		
		if($id > 0){
			
			$sql = "UPDATE #__location_tags SET name='$name', description='$description',marker='$marker',marker_shadow='$marker_shadow',user_tag='$user_tag',`order`=$order,`tag_group`='$tag_group',child_of='$child_of',tag_group_order=$tag_group_order WHERE id = $id";
			
			$db->setQuery($sql);
			$db->Query();

			if($tag_group_order > 0){
				$db->setQuery('UPDATE #__location_tags SET tag_group_order = ' .$tag_group_order. ' WHERE tag_group = \'' . $tag_group . '\''  );
				$db->Query();
			}
			
		}else{
			
			$sql = "INSERT INTO #__location_tags (name,description,marker,marker_shadow,user_tag,`order`,tag_group,child_of,tag_group_order) VALUES ( '$name','$description','$marker','$marker_shadow','$user_tag',$order,'$tag_group','$child_of',$tag_group_order)";
			$db->setQuery($sql);	
			$db->Query();
			$id = (int)$db->insertid();

		}

	}
	
		
	function remove(){
		$db = JFactory::getDBO();
		$ids = JRequest::getVar('cid');
		
		$sql = "DELETE FROM #__location_tags WHERE id in (".implode(",",$ids).")";
		
		$db->setQuery($sql);
		$db->Query();
		

		$sql = "DELETE FROM #__location_tags_link WHERE tag_id in (".implode(",",$ids).")";
		
		$db->setQuery($sql);
		$db->Query();

		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_locator&task=managetags&Itemid=' . JRequest::getInt('Itemid',''),JText::_('Tag(s) deleted!'));	
						
	}
		
	/**
	 * Method to get content item data for the Directory
	 *
	 * @access public
	 * @return array
	 */
	function getTotal()
	{

		return count($this->_data);
	}
	
	
	/**
	 * Method to get content item data for the Directory
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Load the Directory data
		if ($this->_loadData())
		{
			// Initialize some variables
			$user	=& JFactory::getUser();

			// raise errors
		}

		return $this->_data;
	}


	/**
	 * Method to load content item data for items in the Directory
	 * exist.
	 *
	 * @access	private
	 * @return	boolean	True on success
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			// Get the pagination request variables

			$query = $this->_buildQuery();
		
			$i = 0;
			
			$location_id = -1;
			
			$sql = $this->_buildQuery();

			$db = JFactory::getDBO();
			
			$db->setQuery($sql);
			
			$Arows = $db->loadObjectList();

			$this->_data = $Arows;

		}

		return true;
	}
	
	function getParams(){
		
		  $component = JComponentHelper::getComponent( 'com_locator' );
		  $params = new JParameter( $component->params );
		
		  $menuitemid = JRequest::getInt( 'Itemid' );
		  
		  if ($menuitemid)
		  {
		  	if(class_exists('JSite')){
			    $menu = JSite::getMenu();
			    $menuparams = $menu->getParams( $menuitemid );
			    $params->merge( $menuparams );
		  	}
		  }
		 
		if(isset($this->_module_parameters)){
			 $params->merge($this->_module_parameters);
		}
		  
		return $params;
		
	}	
	
	function _buildQuery()
	{
		$mainframe = JFactory::getApplication();
		$id = JRequest::getVar('id',0, '', 'int');
		$task = JRequest::getVar('task');
				
		if($id <=0 && $task != "managetags"){
			$cid = JRequest::getVar('cid');
			$id = $cid[0];	
		}
				
		$order = $this->_buildDirectoryOrderBy();
		$params = $this->getParams();
		$tags = $params->get('tags');
		$tag_list = "";
		
		if(is_array($tags)){
			$tag_list = "(" . implode(",",$tags) . ")";
		}else{
			if($tags > 0){
				$tag_list = "(" . $tags . ")";				
			}
		}
		if(strlen($tag_list) > 0){
			$tag_list = ' WHERE id in '	.$tag_list;
		}
		
		if($id > 0){
			$query = '	SELECT * FROM #__location_tags WHERE id= ' . $id . $order;
		}else{
			$query = 'SELECT * FROM #__location_tags '.$tag_list.' order by tag_group,child_of, name';
			
			
		}
	
		return $query;
	}

	function _buildDirectoryOrderBy()
	{
		 $mainframe = JFactory::getApplication();
		// Get the page/component configuration
		
		$orderby = ' ORDER BY id';
		
		return $orderby;
		
	}
	
	function getTagGroups(){
		
		$tag_id = JRequest::getInt('id',0);
		
		$db=JFactory::getDBO();
		
		$selected = '';
		
		if($tag_id > 0){
			$db->setQuery('SELECT child_of FROM #__location_tags WHERE id = ' .$tag_id);
			$selected = $db->loadResult();
			$query = 'SELECT distinct `name` as `value`,name as `name` FROM #__location_tags WHERE name NOT IN (SELECT name from #__location_tags WHERE id='.$tag_id.') order by name';
		
		}else{
			$query = 'SELECT distinct `name` as `value`, name as `name` FROM #__location_tags order by name';			
		}

		$db->setQuery($query);
		$tag_groups = $db->loadObjectList();
		
		if(count($tag_groups) > 0){

			$tag_select[0]->value = '';		
			$tag_select[0]->name = JText::_('None');
			
			$tag_select = array_merge($tag_select,$tag_groups);

			$this->_lists['child_of'] = JHTML::_('select.genericlist',  $tag_select, 'child_of', 'class="inputbox" size="1"', 'value', 'name',$selected);
		}
		
	}	
	
	
	function getTagList(){
		
		$selected = JRequest::getString('tags');

		$tags = $this->getData();
		
		if(count($tags) > 0){
			
			$tag_select[0]->id = "";
			
			$tag_select[0]->name = JText::_('LOCATOR_SELECT');
			
			$tag_select = array_merge($tag_select,$tags);

			$this->_lists['tags'] = JHTML::_('select.genericlist',  $tag_select, 'tags', 'class="inputbox" size="1"', 'id', 'name',$selected);
		}
		
	}	
	
	function getLists(){
		
		LocatorModelTags::getTagGroups();
		
		$db=JFactory::getDBO();
		$db->setQuery('SELECT MAX(`order`) + 1 as next_order FROM #__location_tags');
		$this->_lists['next_order'] = (int)$db->loadResult();		

		return $this->_lists;
	}
	
	
	function getName(){
		return "tags";	
	}

	function _buildDirectoryWhere()
	{
		 $mainframe = JFactory::getApplication();

		$user		=& JFactory::getUser();
		$gid		= $user->get('aid', 0);
		
		// $now		= $mainframe->get('requestTime');
		$cmd = JRequest::getString('cmd','');	
		
		$where = ' WHERE 1 ';	
		
		if($cmd == "search"){
			
			$keyword = addslashes(JRequest::getString('keyword',''));	
			
			$keywords = explode(" ",$keyword);
	
			$clause =  implode("%' OR '%", $keywords) ;
			
			$where .= " AND (LOWER(l.name) like '%" . strtolower(trim($clause)) . "%') ";
		}

		$jnow		=& JFactory::getDate();
		$now		= $jnow->toMySQL();
		
		$task = JRequest::getString('task','');		
		
		$nullDate	= $this->_db->getNullDate();

		//First thing we need to do is assert that the articles are in the current category
		$where .= ' AND f.published = 1';

		return $where;
	}
	
}