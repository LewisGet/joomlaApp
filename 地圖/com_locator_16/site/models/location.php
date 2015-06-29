<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: location.php 1005 2012-06-04 20:52:47Z fatica $
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
class LocatorModelLocation extends JModel
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

	
	function savegeocode(){
		
		$db = JFactory::getDBO();
		
		$id = (int)JRequest::getInt('id');
		$lng = JRequest::getFloat('lng');
		$lat = JRequest::getFloat('lat');
		$zip = JRequest::getString('postal_code');
		$tld = addslashes(JRequest::getString('tld'));
		
		//TODO: from admin
		if(!$zip){
			$zip = JRequest::getString('PostalCode');
		}
		
		$zip = trim(addslashes($zip));	
		
		if(JRequest::getInt('update',0) == 1 && $id > 0){
				
				//ensure its not in there already
				$sql = "SELECT location_zip_id FROM  #__location_zips WHERE location_id='$id'";
				$db->setQuery($sql);	
				$location_zip_id = $db->loadResult();	

				if($location_zip_id > 0){
					$sql = "UPDATE #__location_zips SET `updated` = NOW() WHERE location_zip_id = $location_zip_id";
					$db->setQuery($sql);
					$db->Query();
				}
		}
				
		if(strlen($lng) > 0){
			
			if($id > 0){
				
				//ensure its not in there already
				$sql = "DELETE FROM  #__location_zips WHERE location_id='$id'";
				$db->setQuery($sql);	
				$db->Query();	

				$sql = "INSERT INTO #__location_zips (zip,lng,lat,location_id,country,updated) VALUES ( '$zip','$lng','$lat','$id','$tld',NOW() )";
				
			}else{
				
				$sql = "INSERT INTO #__location_zips (zip,lng,lat,country,updated) VALUES ( '$zip','$lng','$lat','$tld',NOW())";
			}
			
			//die(print_r($db));
			$db->setQuery($sql);	
			
			$db->Query();
			
		}
		die();
	}
	
	function getTagList(){
		
		$selected = JRequest::getString('tags');
		$c = new LocatorController();
		$model = $c->getModel('tags');
		
		$tags = $model->getData();
		
		if(count($tags) > 0){

			$new = $tags[0];
			
			$tag_select = $tags; //array_merge($tags,array($new));
			
			$tag_select[0]->id = "";
			$tag_select[0]->name = JText::_('LOCATOR_SELECT');

			$this->_lists['tags'] = JHTML::_('select.genericlist',  $tag_select, 'tags', 'class="inputbox" size="1"', 'id', 'name',$selected);
		}
		
	}
	
	function save(){

		$sql = "";

		$mainframe = JFactory::getApplication();
		$id = (int)JRequest::getInt('id');
		$name = addslashes(JRequest::getString('name'));	
		$description =  addslashes(JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW)); 
		$zip = JRequest::getString('PostalCode');
		$zip = trim(addslashes($zip));	
		$published = 0;
		
		$user = JFactory::getUser();
		
		if(!LocatorModelDirectory::hasAdmin()){
			
			$params = &JComponentHelper::getParams( 'com_locator' );
			
			$menuitemid = JRequest::getInt( 'Itemid' );
			  
			if ($menuitemid)
			{
				$menu = JSite::getMenu();
				$menuparams = $menu->getParams( $menuitemid );
				$params->merge( $menuparams );
			}
			
			if($params->get( 'publishautomatically',0 ) == 1){
				$published = 1;
			}
		
			//validate recaptcha
			if($params->get( 'recaptcha',0 ) == 1 && !LocatorModelDirectory::hasAdmin()){
				
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'recaptchalib.php');
				
				$resp = recaptcha_check_answer ($params->get( 'recaptcha_private',0 ),
				                                $_SERVER["REMOTE_ADDR"],
				                                $_POST["recaptcha_challenge_field"],
				                                $_POST["recaptcha_response_field"]);
				
				if (!$resp->is_valid) {
				
					$mainframe->redirect('index.php?Itemid=' . JRequest::getInt('Itemid',''), JText::_('Error, Location not Saved! ' .  $resp->error));
				}
				
			}
		}else{
			$published = JRequest::getString('published');
		}
				
		
		$db = JFactory::getDBO();
		
		if($id > 0){
			
			$sql = "UPDATE #__locations SET name='$name', description='$description', published='$published' WHERE id = $id";
			$db->setQuery($sql);	
			$db->Query();
			
			//delete existing attributes
			$sql = "DELETE FROM #__location_fields_link WHERE location_id = $id";
			$db->setQuery($sql);
			$db->Query();

			//delete existing tags
			$sql = "DELETE FROM #__location_tags_link WHERE location_id = $id";
			$db->setQuery($sql);
			$db->Query();			
				
		}else{
			
			$sql = "INSERT INTO #__locations (name,description,published,user_id) VALUES ( '$name','$description','$published',{$user->id})";
			$db->setQuery($sql);	
			$db->Query();
			$id = (int)$db->insertid();

		}
		
		if($id > 0){
			
			//delete existing lat/lng
			$sql = "DELETE FROM #__location_zips WHERE location_id = $id";
			$db->setQuery($sql);
			$db->Query();		
			
		}
		
		$lng = addslashes(JRequest::getString('lng'));	
		$lat = addslashes(JRequest::getString('lat'));
		

		if(strlen($lng) > 0){
			
			$sql = "INSERT INTO #__location_zips (zip,lng,lat,location_id) VALUES ( '$zip','$lng','$lat','$id')";
			$db->setQuery($sql);	
			$db->Query();
			
		}else{
			
			JError::raiseWarning( 0, "No latitude/longitude stored for this location.");
			
		}

		$sql = "SELECT name,id FROM #__location_fields ORDER BY `order` DESC";
	
		$db->setQuery($sql);
		$rows= $db->loadObjectList();			

		foreach ($rows as $row){
		
			$value =  addslashes(JRequest::getVar(str_replace(" ","",$row->name), '', 'post', 'string', JREQUEST_ALLOWRAW)); 
											
			if(strlen($value) > 0){
				
				$sql = "INSERT INTO #__location_fields_link (location_fields_id,location_id,value)
						VALUES ({$row->id},$id,'$value')";
				
						$db->setQuery($sql);
						$db->Query();					
			}
				
		}	
		
		//add the list of tags
		$tags = JRequest::getVar('tags');
		
		if(!is_array($tags)){
			$tags = array($tags);	
		}
	
		if(count($tags))
		foreach ($tags as $tag){
				$tag = addslashes($tag);
				$sql = "INSERT INTO #__location_tags_link (tag_id,location_id,value)
					VALUES ($tag,$id,'')";
			
					$db->setQuery($sql);
					$db->Query();
		}
			
			
		if(LocatorModelDirectory::hasAdmin()){
			
			$list = $db->getTableList();
			$config = new JConfig();
			
			$precache_table = 'locations_precache';
			$has_precache = false;
			$precache_message =  JText::_('Location Saved!');
			
			$mainframe->redirect('index.php?option=com_locator&view=directory&Itemid=' . JRequest::getInt('Itemid',''),$precache_message);
		}else{
			$mainframe->redirect('index.php?option=com_locator&view=location&layout=form&Itemid=' . JRequest::getInt('Itemid',''), JText::_('Location Saved!'));
		}
	
	}
	
	function saveMultiple(){
		
		$db = JFactory::getDBO();
		
		$ids = JRequest::getVar('cid');
			
		foreach ($ids as $id){
		
			//add the list of tags
			$tags = JRequest::getVar('tags');	

			$id = (int)$id;	
			
			//avoid adding duplicates	
			$sql = "DELETE FROM #__location_tags_link WHERE location_id = '$id'";
			$db->setQuery($sql);
			$db->Query();	
			
			if(count($tags))
			foreach ($tags as $tag){
				
					$tag = (int)$tag;
	
					$sql = "INSERT INTO #__location_tags_link (tag_id,location_id,`value`)
							VALUES ($tag,$id,'')";
					$db->setQuery($sql);
					$db->Query();

			}
			
		}

		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_locator&task=admin&Itemid=' . JRequest::getInt('Itemid','') , JText::_('Location(s) tags updated!'));
			
				
	}
	
	
	function unpublish(){
		$db = JFactory::getDBO();
		$ids = JRequest::getVar('cid');
		
		
		$safe_ids = array();
		
		foreach ($ids as $id){
			$id = (int)$id;
			array_push($safe_ids,$id);
		}		
		
		$sql = "UPDATE #__locations SET published = 0 WHERE id in (".implode(",",$safe_ids).")";

		$db->setQuery($sql);
		$db->Query();
				
		 $mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_locator&task=admin&Itemid=' . JRequest::getInt('Itemid',''),JText::_('Location(s) unpublished!'));	
						
	}
	
	
	function remove(){
		$db = JFactory::getDBO();
		$ids = JRequest::getVar('cid');
		
		$sql = "DELETE FROM #__locations WHERE id in (".implode(",",$ids).")";

		$db->setQuery($sql);
		$db->Query();
		
		
		 $mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_locator&task=admin&Itemid=' . JRequest::getInt('Itemid',''),JText::_('Location(s) Deleted!'));	
						
	}
		
	
	function publish(){
		$db = JFactory::getDBO();
		$ids = JRequest::getVar('cid');
		
		$safe_ids = array();
		
		foreach ($ids as $id){
			$id = (int)$id;
			array_push($safe_ids,$id);
		}
		
		$sql = "UPDATE #__locations SET published = 1 WHERE id in (".implode(",",$safe_ids).")";

		$db->setQuery($sql);
		$db->Query();
				
		 $mainframe = JFactory::getApplication();
		
		$mainframe->redirect('index.php?option=com_locator&task=admin&Itemid=' . JRequest::getInt('Itemid',''),JText::_('Location(s) Published!'));
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
	 * Method to get content item data for the Directory
	 *
	 * @access public
	 * @return array
	 */
	function getLists()
	{
		if(isset($this->_lists)){
			return $this->_lists;
		}
	}	

	/**
	 * Method to load content item data a location
	 * exist.
	 *
	 * @access	private
	 * @return	boolean	True on success
	 */
	function _loadData($id = 0)
	{

			// Get the pagination request variables
			$i = 0;
			$limit		= JRequest::getVar('limit', 10, '', 'int');
			$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');			
			$location_id = -1;
			
			$params = &JComponentHelper::getParams( 'com_locator' );
			
			$menuitemid = JRequest::getInt( 'Itemid' );
			  
			if ($menuitemid)
			{
				$menu = JSite::getMenu();
				$menuparams = $menu->getParams( $menuitemid );
				$params->merge( $menuparams );
			}
						
			if(!$id){
				
				$id = (int)JRequest::getInt('id');
				
				if(!$id){
					$id = $params->get('id');
				}
				
				if(!$id){
					//this happens when the tag screen is loaded and it's o.k.
					//JError::raiseError('404',JText::_('NOT_FOUND'));
				}
			}
			
			
			$sql = $this->_buildQuery($id);

			$db = JFactory::getDBO();
			
			$db->setQuery($sql);
			
			$Arows = $db->loadObjectList();
		
			$rows = array();
			
			$j = 0;
			
			if(count($Arows) > 0){
							
				foreach ($Arows as $row)
				{
					
					if($j == 0){
						$rows[$i] = $row;						
					}
					
					if(strlen(@$row->title) > 0)
					{

							$rows[$i]->description = $row->description;
							
							//$row->description = $row->description;htmlentities($row->description, ENT_QUOTES, 'UTF-8');
							$rows[$i]->title = htmlentities($row->title, ENT_QUOTES, 'UTF-8');
							
					}
					
					//added if the first field is empty
					if(@$row->id > 0){
						$rows[$i]->id = $row->id;
					}
					
					//added if the first field is empty
					if(strlen(@$row->lat) > 0){
						
						$rows[$i]->lat = $row->lat;
						$rows[$i]->lng = $row->lng;
					}
					
					//added if the first field is empty
					if(strlen(@$row->published) > 0){
						$rows[$i]->published = $row->published;
					}
					
					
					$rows[$i]->fields[$j]->name = $row->name;
					$rows[$i]->fields[$j]->type = $row->type;
					
					if(isset($row->value)){
		
						$rows[$i]->fields[$j]->value = $row->value;
					}
					
					$j++;
	
				}
				
				$this->_data = $rows;
				
			}
			
			if($id <= 0){
				$id = (int)JRequest::getInt('id',0);
			}
		
			if($id > 0){
				$db = JFactory::getDBO();
				
				$id = (int)$id;
				$q = "SELECT t.id 
								FROM #__location_tags t
								LEFT JOIN #__location_tags_link l 
								ON l.tag_id = t.id
								WHERE location_id = $id";
				
				$db->setQuery($q);
		
				$selected = $db->loadObjectList();
			}else{
				$selected = '';
			}
			
			$where = '';
						
			$mainframe = JFactory::getApplication();
			
			$where = " WHERE 1 ";
			
			if($mainframe->isSite()){
				$where .= " AND (t.user_tag is null or t.user_tag = 1) ";
			}

			$only = $params->get('tags');
			
			if($only){
				if(!is_array($only)){
					$only = array($only);	
				}
				$where .= " AND t.id in (" . implode(',',$only) . ")" ;
			}		
			
			$sql  = 'SELECT * FROM #__location_tags t' . $where;
				
			$db->setQuery($sql);
			$tags = $db->loadObjectList();	
			
			if(count($tags)){
				$this->_lists['tags'] = JHTML::_('select.genericlist',  $tags, 'tags[]', 'class="inputbox" multiple="multiple" size="8"', 'id', 'name',$selected);
			}
									
				
		return true;
	}
	
	function _buildQuery($id)
	{
		$mainframe = JFactory::getApplication();
		
		if($id <=0 ){
			
			$cid = JRequest::getVar('cid');

			$id = (int)$cid[0];	
		}
		
		
		$tag_list_clause = '';
						
		//build the tag clause
		$db = JFactory::getDBO();
		$db->setQuery('SELECT max(name) as `name`,`order` FROM #__location_tags WHERE `order` > 0 group by `order` order by `order` ');
		$tag_rows = $db->loadObjectList();
		
		if(count($tag_rows) > 0){
			
			$tag_list_clause = ' ,(SELECT concat(';

			foreach ($tag_rows as $tag_row){
			
				$tag_row_order = (int)$tag_row->order;
				$tag_list_clause .= " max(case t.`order` when " . $tag_row_order . " then concat(t.`name`,',') else '' end)," ;
					
			}
			
			$tag_list_clause = rtrim($tag_list_clause,',');
			
			$tag_list_clause .= ') 
						FROM  
							#__location_tags_link tl 
						LEFT JOIN
							#__location_tags t 
						ON t.id=tl.tag_id
						WHERE 
						tl.location_id = l.id 
						) as taglist ';
		}

		//get the top tag as the marker.  (We have to pick one!)
  		$tag_fields = ", (SELECT  
					marker
					FROM  
					#__location_tags_link tl 
					LEFT JOIN
					#__location_tags t ON t.id=tl.tag_id
					WHERE tl.location_id = l.id 
					ORDER BY `order` DESC LIMIT 1 ) AS marker,
					(SELECT  
					marker_shadow
					FROM 
					#__location_tags_link tl 
					LEFT JOIN
					#__location_tags t ON t.id=tl.tag_id
					WHERE tl.location_id = l.id 
					ORDER BY `order` DESC LIMIT 1 ) AS marker_shadow";	
		
		//prevent front-end editting
		$mainframe = JFactory::getApplication();
			
		if(!LocatorModelDirectory::hasAdmin() && JRequest::getString('layout') == "form"){
			$id = -1;
		}
				
		$order = $this->_buildDirectoryOrderBy();

		if($id > 0){
			
			$id = (int)$id;
			
			$query = '	SELECT l.*,l.name as title,i.value,f.name,f.order,z.lat,z.lng,f.type'.$tag_fields.' ' . $tag_list_clause . '
						FROM #__location_fields f
						LEFT JOIN #__location_fields_link i ON i.location_fields_id = f.id and i.location_id = '.$id.'
						LEFT JOIN #__locations l  on l.id = i.location_id
						LEFT JOIN #__location_zips z on z.location_id = l.id 
						' . $order;	
			
			//die($query);
	
		}else{
			$query = '	SELECT f.name,f.order,f.type FROM #__location_fields f WHERE f.user_field=1 order by f.order';
		}
		
		//guest accesss, no edit ability
		if((!LocatorModelDirectory::hasAdmin() || JRequest::getInt('framed',0) == 1) &&  JRequest::getString('layout') == 'form'){
			$query = '	SELECT f.name,f.order,f.type FROM #__location_fields f WHERE f.user_field=1 and f.visitor_field=1 order by f.order';
			
		}

		
		return $query;
	}

	function _buildDirectoryOrderBy()
	{
		 $mainframe = JFactory::getApplication();
		// Get the page/component configuration
		
		$orderby = ' ORDER BY f.order';
		
		return $orderby;
		
	}
	
	
	/**
	 * Returns the distance formula clause used in the where and select statements
	 *
	 */
	function _buildDirectoryDistance(){
		
		$task = JRequest::getString('task','');		
		$clause = "";
		
		switch ($task) {
			
			case 'search_zip':

				//get the lat/long of the zip code of interest
				$zip = JRequest::getInt('postal_code',0);
								
				if($zip > 0){

					$sql = "SELECT lat,lng FROM #__location_zips WHERE zip='$zip'";
					$db = JFactory::getDBO();
					
					$db->setQuery($sql);
					$row = $db->loadObject();
					
					
					if($row->lng > 0){
						//TODO, check for KM or Miles in config		  1.609344 * 6378.388 			
						$clause = "10265.02046 / 2.5 * ACOS(SIN(RADIANS({$row->lat})) * SIN(RADIANS(lat)) + COS(RADIANS({$row->lat})) * COS(RADIANS(lat)) * COS(RADIANS(lng) - RADIANS({$row->lng})))" ;
												
					}else{
						//JError::raiseWarning(301,'No lat/long found for the provided zip code, please try again');
					}
					
				}else{
					//JError::raiseWarning(301,'Zip code not found, please try again');
				}
				
							
				break;
		
			default:
			break;
		}
		return $clause;
	}
	
	function getName(){
		return "directory";	
	}

	function _buildDirectoryWhere()
	{
		 $mainframe = JFactory::getApplication();

		$user		=& JFactory::getUser();
		$gid		= $user->get('aid', 0);
		
		// TODO: Should we be using requestTime here? or is JDate ok?
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