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
class LocatorModelFields extends JModel
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
		
		$id 		= (int)JRequest::getInt('id');
		$published	= (int)JRequest::getInt('published',1);
		$order 		= (int)JRequest::getInt('order',0);
		$name 		= addslashes(JRequest::getString('name'));	
		$type = addslashes(JRequest::getString('type'));
			
		$user_field		= (int)JRequest::getInt('user_field',1);
		$visitor_field		= (int)JRequest::getInt('visitor_field',1);
		
		$db = JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		
		if($id > 0){
			
			$sql = "UPDATE #__location_fields SET name='$name', type='$type',`order`='$order',published='$published',user_field='$user_field',visitor_field='$visitor_field' WHERE id = $id";
			
			$db->setQuery($sql);
			if(!$db->Query()){
				$mainframe->enqueueMessage($db->ErrorMsg());				
			}

		}else{
			//no inserted fields are "Core"
			$sql = "INSERT INTO #__location_fields (name,type,`order`,published,iscore,user_field,visitor_field) VALUES ( '$name','$type',$order,$published,0,$user_field,$visitor_field)";
			$db->setQuery($sql);	
			$db->Query();
			
			
			
			$id = (int)$db->insertid();

		}
		
		
		$mainframe->redirect('index.php?option=com_locator&task=managefields&Itemid=' . JRequest::getInt('Itemid',''),JText::_('Field Saved'));
	
	}
	
	
	function set_userfield(){
		
		$id 		= JRequest::getVar('cid');
		$id = (int)$id[0];
		$db = JFactory::getDBO();
		if($id > 0){
		$sql = "UPDATE #__location_fields SET visitor_field='1' WHERE id = $id";
		$db->setQuery($sql);
		$db->Query();
		}
		
		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_locator&view=fields&Itemid=' . JRequest::getInt('Itemid',''),JText::_('Field Updated'));
		
	}
	
	function unset_userfield(){
		
		$id 		= JRequest::getVar('cid');
		$id = (int)$id[0];
		$db = JFactory::getDBO();
		if($id > 0){
			$sql = "UPDATE #__location_fields SET visitor_field='0' WHERE id = $id";
			$db->setQuery($sql);
			$db->Query();
		}
		
		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_locator&view=fields&Itemid=' . JRequest::getInt('Itemid',''),JText::_('Field Updated'));
		
	}
		
	function remove(){
		$db = JFactory::getDBO();
		$ids = JRequest::getVar('cid');
		
		$sql = "DELETE FROM #__location_fields WHERE id in (".implode(",",$ids).")";
		
		$db->setQuery($sql);
		$db->Query();

		$sql = "DELETE FROM __location_fields_link WHERE field_id in (".implode(",",$ids).")";
		$db->setQuery($sql);
		$db->Query();
		
		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_locator&task=managefields&Itemid=' . JRequest::getInt('Itemid',''),JText::_('Fields deleted!'));	
						
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
		$this->_loadData();
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
	
	function _buildQuery()
	{
		 $mainframe = JFactory::getApplication();
		
		$id = JRequest::getVar('id',0, '', 'int');
		
		$task = JRequest::getVar('task');
				
		if($id <=0 && $task != "managefields"){
			$cid = JRequest::getVar('cid');
			$id = $cid[0];	
		}
				
		$order = $this->_buildDirectoryOrderBy();

		if($id > 0){
			$query = '	SELECT * FROM #__location_fields WHERE id= ' . $id . $order;
		}else{
			$query = '	SELECT * FROM #__location_fields order by id';
		}
	
		return $query;
	}

	function _buildDirectoryOrderBy()
	{
		$orderby = ' ORDER BY id';
		
		return $orderby;
		
	}
		
	function getFieldList(){
		
		$selected = JRequest::getString('fields');

		$tags = $this->getData();
		
		if(count($tags) > 0){
			
			$tag_select[0]->id = "";
			
			$tag_select[0]->name = JText::_('LOCATOR_SELECT');
			
			$tag_select = array_merge($tag_select,$tags);

			$this->_lists['fields'] = JHTML::_('select.genericlist',  $tag_select, 'fields', 'class="inputbox" size="1"', 'id', 'name',$selected);
		}
		
	}	
	
	function getLists(){
		
				
		$db=JFactory::getDBO();
		$db->setQuery('SELECT MAX(`order`) + 1 as next_order FROM #__location_fields');
		$this->_lists['next_order'] = (int)$db->loadResult();	

		return $this->_lists;
	}
	
	
	function getName(){
		return "fields";	
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

		return $where;
	}
	
}