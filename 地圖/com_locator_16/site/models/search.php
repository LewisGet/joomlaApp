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
class LocatorModelSearch extends JModel
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

	var $_lists = null;

	function __construct()
	{
        
        parent::__construct();
 
         $mainframe = JFactory::getApplication();
         $option = 'com_locator';
 
        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
         
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);        
        
        $filter_order     = $mainframe->getUserStateFromRequest( $option.'filter_order', 'filter_order', 'search_id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
 
        $this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);
        
        
	}
		
	function save(){

		$task 	= JRequest::getString('task','');	
		$zip  	= JRequest::getString('postal_code','');	
		$tags  	= JRequest::getString('tags','');	
		$layout = JRequest::getString('layout','');
		$states  	= JRequest::getString('states','');
		$country  	= JRequest::getString('country','');
		$keyword 	= addslashes(JRequest::getString('keyword',''));
		$radius 	= JRequest::getFloat('radius',0);	
		$city  		= addslashes(JRequest::getString('city',''));
		
		$db = JFactory::getDBO();
		
		//ya never know
		$ip = addslashes($_SERVER['REMOTE_ADDR']);
		
		$sql = "INSERT INTO #__location_search (term,type,city,state,country,postal_code,keyword,tag,IP,radius)
				VALUES ('','','$city','$states','$country','$zip','$keyword','$tags','$ip','$radius')";
		
		$db->setQuery($sql);	
		$db->Query();
			
	}
	
		
	function remove(){
		$db = JFactory::getDBO();
		$ids = JRequest::getVar('cid');
		
		$sql = "DELETE FROM #__location_search WHERE id in (".implode(",",$ids).")";
		
		$db->setQuery($sql);
		$db->Query();
		
		 $mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_locator&task=showsearch&Itemid=' . JRequest::getInt('Itemid',''),JText::_('Tag(s) deleted!'));	
						
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
		    $menu = JSite::getMenu();
		    $menuparams = $menu->getParams( $menuitemid );
		    $params->merge( $menuparams );
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
				
		if($id <=0 && $task != "showsearch"){
			$cid = JRequest::getVar('cid');
			$id = $cid[0];	
		}
				
		$order = $this->_buildOrderBy();
		
		$params = $this->getParams();
		
		$types = $params->get('type');
		
		$type_list = "";
		
		if(is_array($types)){
			$type_list = "(" . implode(",",$types) . ")";
		}
		if(strlen($type_list) > 0){
			$type_list = ' WHERE id in '.$type_list;
		}
		
		if($id > 0){
			$query = 'SELECT * FROM #__location_search WHERE id= ' . $id . $order;
		}else{
			$query = 'SELECT * FROM #__location_search '.$type_list.' ' . $order;
		}

		return $query;
	}

	function _buildOrderBy()
	{
	
         $mainframe = JFactory::getApplication();
 
    	$orderby = '';
    	
        $filter_order     = $this->getState('filter_order');
        
        $filter_order_Dir = $this->getState('filter_order_Dir');
        
        if(!empty($filter_order) && !empty($filter_order_Dir)){
        	$orderby = ' ORDER BY '.$filter_order.' ' . $filter_order_Dir;
        }
        
		return $orderby;
		
	}
		
	function getLists(){

		return $this->_lists;
	}
	
	
	function getName(){
		return "search";	
	}

	function _buildDirectoryWhere()
	{
		 $mainframe = JFactory::getApplication();

		$user		=& JFactory::getUser();
		$gid		= $user->get('aid', 0);
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

		$where .= ' AND f.published = 1';

		return $where;
	}
	
}