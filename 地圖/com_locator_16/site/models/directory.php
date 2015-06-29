<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software tc
 * $Id: directory.php 1017 2012-07-30 17:36:42Z fatica $
 * tc
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
class LocatorModelDirectory extends JModel
{
	/**
	 * Directory data array
	 *
	 * @var array
	 */
	var $_data = null;
	
	var $_module_parameters = null;

	/**
	 * Directory total
	 *
	 * @var integer
	 */
	var $_total = null;
	
	/**
	 * 
	 * @var unknown_type
	 */
	var $_lists = null;
	
	function __construct()
	{
        
		parent::__construct();
 
		$option = 'com_locator';
        $mainframe = JFactory::getApplication();
        
        $params = LocatorModelDirectory::getParams();
        
        $list_limit = $params->get('page_list_default',100);
        	        
		// Get pagination request variables
		if(LocatorModelDirectory::hasAdmin()){
			$list_limit = $params->get('list_length', $mainframe->getCfg('list_limit'));
		}
				
		$limit = JRequest::getVar('limit', $list_limit, 'int');
		
		if($mainframe->isAdmin()){
			$limit = $mainframe->getUserStateFromRequest($option . '.list.limit', 'limit', $list_limit, 'int');
		}
				
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

			
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	
	
        $filter_order     = $mainframe->getUserStateFromRequest( $option.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
 
        $this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);

	}
	
	function setModuleParams($params){
		
		$this->_module_parameters = $params;
	}
	
	
	function getParams(){
		
		jimport( 'joomla.html.parameter' );
		
		$component = JComponentHelper::getComponent( 'com_locator' );
		$params = new JParameter( $component->params );
		
		$menuitemid = JRequest::getInt( 'Itemid' );
		  
		if ($menuitemid)
		{
			$menu = JSite::getMenu();
			$menuparams = $menu->getParams( $menuitemid );
			$params->merge( $menuparams );
		}
		
		return $params;
	
		
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
		}

		return $this->_data;
	}

	/**
	 * Method to get the total number of content items for the Directory
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{

			$this->_total = $this->_getListCount();
		}
		
		return $this->_total;
	}
	

	/***
	 * Setup the geocoding screen
	 */
	function geocode(){
		
		$c = new LocatorController();
		$model = $c->getModel('location');

		$ids = JRequest::getVar('cid');

		$address = '';
		$address2 = "";
		$city = "";
		$state = "";
		$postalcode = "";
		$country = "";
		$tld= "";

		if(!count($ids)){
			
			//get all from the database
			$sql = "SELECT id FROM #__locations";
			$db = JFactory::getDBO();
			$db->setQuery($sql);
			$ids = $db->loadResultArray();
						
		}
				
		
		$params = LocatorModelDirectory::getParams();
		
		if(count($ids)){
			
			foreach($ids as $id){		

			  $model->_loadData($id);
				
			  if($params->get('skip_geocoded') == 1 && strlen($model->_data[0]->lat) > 0 && strlen($model->_data[0]->lng) > 0){
			  	continue;
			  }
			  			 
				foreach ($model->_data[0]->fields as $field){
								
					//$tld = '';
					
					
					if(isset($field->value)){
						
						switch(strtolower($field->name)){
							
							case 'address':{
								$address = str_replace(" ","+",$field->value);
							}break;
							
							case 'address2':{
								$address2 = str_replace(" ","+",$field->value);
							}break;
							
							case 'city':{
								$city = str_replace(" ","+",$field->value);
							}break;
							case 'state':{
								$state = str_replace(" ","+",$field->value);
							}break;			
							case 'postalcode':{
								$postalcode = str_replace(" ","+",$field->value);
							}break;	
							case 'tld':{
								
								$tld = trim($field->value);
							}break;	
							case 'country':{
								$country = str_replace(" ","+",$field->value);
							}break;																													
						}
					}
				}
				
				$geostring = "";
				
				if($params->get('use_address_1',1) == 1){
					$geostring = $address;
				}
				
				if($params->get('use_address_2',0) == 1){
					$geostring .= '+' . $address2;
				}
		
				if($params->get('use_city',1) == 1){
					$geostring .= '+' . $city;
				}
				
				if($params->get('use_state',1) == 1){
					$geostring .= '+' . $state;
				}
				
				if($params->get('use_postalcode',1) == 1){
					$geostring .= '+' . $postalcode;
				}
				
				if($params->get('use_country',1) == 1){
					$geostring .= '+' . $country;
				}
				
		
				$this->_lists['geocode']["$id"] = addslashes($geostring);
								
				$address = $address2 = $city = $state = $postalcode = $country = $tld = "";
				
				//TODO: replace this with a regex for crying out loud ;-)
				if(strlen(trim($tld)) > 0){
					$this->_lists['tld']["$id"] = $tld;
				}else{
					$this->_lists['tld']["$id"] = $params->get('gmap_base_tld','US');
				}
				
				
			
				$this->_lists['geocode']["$id"]	 = str_replace("++","+",$this->_lists['geocode']["$id"]	);
				$this->_lists['geocode']["$id"]	 = str_replace("+++","+",$this->_lists['geocode']["$id"] );
				$this->_lists['geocode']["$id"]	 = trim($this->_lists['geocode']["$id"]	,"+");
				$this->_lists['geocode']["$id"]	 = trim($this->_lists['geocode']["$id"]	,"+");
				
				
			}

			
		}

	}
	
	function clearCache(){
		
		$user = JFactory::getUser();
		
		if($user->usertype == "Super Administrator"){
				
			$db = JFactory::getDBO();
	
			$sql = "DELETE FROM #__location_zips WHERE (location_id = '' or location_id is null)";
			$db->setQuery($sql);
			$db->Query();		
			
		}
	
	}
	
	function savelatlng(){
		$db = JFactory::getDBO();
		
		$location_id = JRequest::getInt('location_id',0);
		
		if($location_id){
									
			$lat = addslashes(JRequest::getString('lat'));
			$lng = addslashes(JRequest::getString('lng'));
			
			if(abs($lat) > 0){
				$sql = "INSERT INTO #__location_zips (lng,lat,location_id)	VALUES ($lng,$lat,$id)";
				$db->setQuery($sql);
				$db->Query();		
			}
			
		}
		
	}
	
    function lower(&$string){
  		$string = strtolower($string);
 	}
 	
	/**
	 * Method to get content item data for the Directory
	 *
	 * @access public
	 * @return array
	 */
	function getLists()
	{
		return $this->_lists;
	}
 	
	function hasAdmin(){

		$mainframe 	= JFactory::getApplication();
		$user 		= JFactory::getUser();
		
		if($mainframe->isAdmin()){
			return true;	
		}
		
		if(JRequest::getInt('framed',0) == 1){
			return false;	
		}
		if($user->id > 0){

			return true;	
		}
	
		
		return false;
	}
	
	
	function exportLocations($header_only = false){
				
		$params = LocatorModelDirectory::getParams();
		$segment = $params->get('export_segment','1000');;
		$sep = $params->get('separator',',');
		$date = date("M-d-Y");
		$row_template = '';
					
		header("Content-type: application/octet-stream");
			
		$ext = '.csv';
		
		if($sep != ","){
			$ext = '.txt';
		}
			
		header("Content-Disposition: attachment; filename=\"export".$date.$ext ."\"");	
			
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_locator' . DS . 'view.php');
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_locator' . DS . 'views' . DS . 'directory' . DS.  'view.html.php');
				
		$v = new LocatorViewDirectory();
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__location_fields ORDER BY `order`");
		$header_rows = $db->loadObjectList();
				
		$template = '"{name}"'.$sep.'"{description}"'.$sep.'"{published}"'.$sep .'"{lat}"'.$sep.'"{lng}"' . $sep;	
		
		foreach ($header_rows as $h) {
			$template .= '"{' . preg_replace('/[^a-zA-Z0-9\s]/', '', str_replace(" ","",strtolower($h->name))) . '}"' . $sep;
		}
		
		$db->setQuery("SELECT * FROM #__location_tags ORDER BY `order`");
		$tag_rows = $db->loadObjectList();

		$tag = 0;
		foreach ($tag_rows as $h) {
			$tag++;
			$template .= '"{tag' . $tag . '}"' . $sep;
		}
		
		$template = rtrim($template,$sep);
		$template .= "\r\n";
		
		$db->setQuery("SELECT COUNT(*) FROM #__locations");
		$total = (int)$db->loadResult();
			
		//output header row		
		
		echo strtolower(str_replace(array('{','}'),'',$template));

		if($header_only === true){
			return;	
		}
		
		for($offset = 0; $offset < $total; $offset += $segment){
		
			//echo "Memory is " . memory_get_usage();
			
			$query = $this->_buildQuery();
				
			$Arows = $this->_getList($query, $offset, $segment);
			
			foreach ($Arows as $r){
						
				$r->fields = $this->getFields($r->id);
		
				$r->fulltext = $r->description;
				
				$r->tags = $this->getTags($r->id);
								
				$row_template = $template;
				
				//replace our tags with value in this row
				if($r->tags){
					foreach ($r->tags as $k=>$v){
					
						$off = $k+1;
						$row_template = str_replace('{tag' . $off . '}',$v->name,$row_template);						
					}
				}
		
				//clear out other tags in this row
				if($tag_rows){
					foreach ($tag_rows as $k=>$v){
						$off = $k+1;
						$row_template  = str_replace('{tag' .$off . '}','',$row_template );						
					}
				}			
			
				echo LocatorViewDirectory::formatItem($r,$params,$row_template ,1) . "\r\n";
			}
				 
			unset($Arows);
					
			ob_flush();	

		}
	
		die();
		//echo "Metcmory is " . memory_getct_usage();
	}
	
	/**
	 * Export CSV
	 */
	
	function edxportLocations($header_only = false){
		
		
		$db = JFactory::getDBO();
		
		$delete = JRequest::getInt('delete',0);
		$user = JFactory::getUser();	
		$id ="";
		$csv = "";
		$firstrow = true;
		$header = "";
		$title = "";
		$description = "";
		$lat = 0;
		$lng = 0;
		$published = 0;
		$header_built = false;
		$bubble = 0;
		$row_built = false;
		$dynamic_header = "";
		$field_count = 0;
		$i = 0;	
		$mainframe = JFactory::getApplication();
		$row_offset = 0;
		$segment = 5;
		$row_count = 0;
		$params = LocatorModelDirectory::getParams();
		
		$sep = $params->get('separator',',');
						
		if(LocatorModelDirectory::hasAdmin()){
			
			$db->setQuery("SELECT * FROM #__location_fields ORDER BY `order`");
			$header_rows = $db->loadObjectList();
			
			foreach ($header_rows as $header_row){

				$dynamic_header .= str_replace(" ","",$header_row->name) . $sep;	
				
				$field_count++;
					
			}
			
			$db->setQuery("SELECT * FROM #__location_tags ORDER BY `order`");
			$tag_rows = $db->loadObjectList();
				
			$date = date("M-d-Y");
			
			//header("Content-type: application/octet-stream");
			
			$ext = '.csv';
			
			if($sep != ","){
				$ext = '.txt';
			}
			
			//header("Content-Disposition: attachment; filename=\"export".$date.$ext ."\"");			

			//output the header
			echo "Name".$sep."Description".$sep."Published".$sep."" .  trim($dynamic_header) . "lat".$sep."lng";
			
			echo $sep;
			
			$x = 0;
			foreach ($tag_rows as $tag_row){
				$x++;
				
				echo 'tag' . $x;  //$tag_row->name;	
				
				if($x < count($tag_rows)){
					echo $sep;
				}
			}
		
			if($header_only === true){
				
				if(count($tag_rows) == 0){
					echo "tag1".$sep."tag2".$sep."tag3";
				}

				echo "\r\n";
				die();	
			}
						
			echo "\r\n";
						
			$sql = $this->_buildExportQuery(0,1);

			$db->setQuery($sql);
			$db->loadObjectList();
			$db->setQuery('SELECT FOUND_ROWS();');
			$row_count = 25;// (int)$db->loadResult();
							
			for ($t = 0; $t < $row_count; $t+=$segment){
							
				$sql = $this->_buildExportQuery($t,$segment);
	
				die(str_replace("#__","jos_",$sql));
				$db->setQuery($sql);
				
				$rows = $db->loadObjectList();
	
				$row_offset += $segment;
				
				if(count($rows)){
					
					foreach ($rows as $row){
										
						if($i < $field_count){
													
							if($row->published > 0){
								$published = (int)$row->published;
							}
							
							//build the CSV row		
							if(strlen($row->title) > 0){
								$title = $row->title;
							}
							
							//build the description
							if(strlen($row->description) > 0){
								$description = str_replace("\r\n","",utf8_decode( utf8_encode ( $row->description)));
							}
							
							$x = 0;
							foreach ($tag_rows as $tag_row){
								$x++;
								if(strlen(@$row->{"tag$x"}) > 0){
									${"tag$x"} = $row->{"tag$x"};
								}
							}
							
							if(strlen($row->lat) > 0){
								$lat = $row->lat;
								$lng = $row->lng;
							}
						
							$csv .= '"' .  trim($row->value,"\r\n") .  '"' . $sep;					
							
							$i++;	
																			
						}
						
						if($i == $field_count){
							
							$i = 0;	
												
							echo '"' . $title  .'"' . $sep;
							echo '"' . $description  .'"' . $sep;
							echo '"' . $published  .'"' . $sep;
							
							//ouput dynamic fields
							echo $csv; //substr( $csv,0,strlen($csv) -1 ); //,",");
							
							echo '"' . $lat  .'"' . $sep;
							echo '"' . $lng .'"' . $sep;
							
							$x = 0;
							foreach ($tag_rows as $tag_row){
								$x++;
								echo '"' . @${"tag$x"}  .'"';
								if($x < count($tag_rows)){
									echo $sep;
								}
							}
	
							echo "\r\n";
							
							$csv = "";
							$title = $description = $published = $lat = $lng = "";					
							
							$x = 0;
							foreach ($tag_rows as $tag_row){
								$x++;
								@${"tag$x"} = '';
							}
	
						}
						
					}
				
				}//added
			
				die($sql);	
			}
			die();
		}else{
			JError::raiseError('500',JText::_('NOT_AUTH'));
		}
	}
	
	/**
	 * Import a set of locations via CSV
	 */
	function importUpload(){
		$db = JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		
		$delete = JRequest::getInt('delete',0);
		$user = JFactory::getUser();
		
		$params = LocatorModelDirectory::getParams();
		
		$sep = $params->get('separator',',');
		
		$location_count = 0;
		$field_count = 0;
		
		if(isset($_POST['upload']) && $_FILES['userfile']['size'] > 0 && LocatorModelDirectory::hasAdmin())
		{
			
			$fileName = $_FILES['userfile']['name'];
			$tmpName  = $_FILES['userfile']['tmp_name'];
			$fileSize = $_FILES['userfile']['size'];
			$fileType = $_FILES['userfile']['type'];
					
			$row = 0;
			
			$message = "";
			
			if (($handle = fopen($tmpName, "r")) !== FALSE) {
				
			    while (($data[$row] = fgetcsv($handle, 5000, $sep)) !== FALSE) {
			        
			    	//check for a legitimate end of the header row
			    	if($row == 0){
				    
			    		if(strtolower($data[$row][0]) != "name"){
			    			$message .= "'Name' column expected in position 1 and not found<br/>";
			    		}
			    		if(strtolower($data[$row][1]) != "description"){
			    			$message .= "'Description' column expected in position 2 and not found<br/>";
			    		}
			    		if(strtolower($data[$row][2]) != "published"){
			    			$message .= "'Published' column expected in position 3 and not found<br/>";
			    		}	

			    		if(strlen($message) > 0){
				    
				    		$mainframe = JFactory::getApplication();
							$mainframe->redirect('index.php?option=com_locator&task=admin&Itemid=' . JRequest::getInt('Itemid',''), JText::_('Imported Failed. Invalid file format. Check that each entry is on one line and that all columns are present as in the example provided.' . $message));
	
				    	}else{
										    		
							if($delete == 1){
									
								$sql = "DELETE FROM #__locations";
								$db->setQuery($sql);	
								$db->Query();	
				
								$sql = "DELETE FROM #__location_zips";
								$db->setQuery($sql);	
								$db->Query();	
				
								$sql = "DELETE FROM #__location_fields_link";
								$db->setQuery($sql);	
								$db->Query();			
				
								$sql = "DELETE FROM #__location_tags_link";
								$db->setQuery($sql);	
								$db->Query();					
	
							}
				    	}
			    	}
			    	
			    	if($row != 0){

			    		$id = 0;
			    		
						//INSERT the location record
						$name		 = addslashes($data[$row][0]);
						$description = addslashes($data[$row][1]);
						$published 	 = (int)addslashes($data[$row][2]);
						
						 
						if(strlen($name) > 0){
							$sql = "INSERT INTO #__locations (name,description,published) VALUES ( '$name','$description','$published')";
							
							$db->setQuery($sql);	
							$db->Query();
							
							$id = (int)$db->insertid();
						}
			    								
						if($id > 0){
							
							$id = (int)$id;
							
							$location_count++;
							
							//get the list of available fields
							$sql = "SELECT name,id FROM #__location_fields ORDER BY `order` ASC";
					
							$db->setQuery($sql);
							
							$rows = $db->loadObjectList();	
							

							foreach ($rows as $r){
								
								//find the position of this field in the header row
			
								$offset = array_search(strtolower(str_replace(" ","",$r->name)),array_map('strtolower',$data[0])); 
								
								if($offset !== false){
													
									$offset = (int)$offset; 
									
									$value = addslashes($data[$row][$offset]);
																				
									if(strlen($value) > 0){
										
										$value = $value;
										
										$sql = "INSERT INTO #__location_fields_link (location_fields_id,location_id,value) VALUES ({$r->id},$id,'$value')";
										
										$db->setQuery("SET NAMES 'utf8'");
										$db->Query();
										
										$db->setQuery($sql);
										$db->Query();

										$field_count++;
									}
									
								}else{
									//die("Did not find column " . print_r($r,1) . print_r($data[0],1));	
								}
							}

						//find the position of this field in the header row
						
						$lat_offset = array_search('lat',array_map('strtolower',$data[0])); 
						$lng_offset = array_search('lng',array_map('strtolower',$data[0])); 
						
						if($lng_offset !== false && $lat_offset !== false){
								
							$value = addslashes($data[$row][$lng_offset]);
																		
							if(strlen($value) > 0){
								
								$lat = addslashes($data[$row][$lat_offset]);
								$lng = addslashes($data[$row][$lng_offset]);
								
								$sql = "INSERT INTO #__location_zips (lng,lat,location_id)
										VALUES ($lng,$lat,$id)";
								
								$db->setQuery($sql);
								$db->Query();
							}									
						}
						
						$tag = 1;
						$tag_offset = 0;
							
							while(array_search('tag' . $tag, $data[0]) !== false){
								
								$tag_offset = array_search('tag' . $tag,$data[0]);
								
								$tag++;
								
								$tag_name = addslashes($data[$row][$tag_offset]);
								
								 if(strlen($tag_name) > 0){
									//does this tag already exist?
									$sql = "SELECT id FROM #__location_tags WHERE name='$tag_name' LIMIT 1";
									$db->setQuery($sql);
									$tag_id = (int)$db->loadResult();
									
									//tag doesn't exist, insert it
									if($tag_id == 0){
										
										//get the next order
										$sql = "SELECT max(`order`)+1 as tag_order from #__location_tags";
										$db->setQuery($sql);
										$tag_order = (int)$db->loadResult();
										
										$sql = "INSERT INTO #__location_tags (name,`order`) VALUES ('$tag_name',$tag_order)";
										$db->setQuery($sql);	
										$db->Query();
										$tag_id = (int)$db->insertid();							
									}
									
									//we now have a tag id, insert the linked rows
									if($tag_id > 0){
											
										$sql = "INSERT INTO #__location_tags_link (tag_id,location_id) VALUES ($tag_id,$id)";
										$db->setQuery($sql);
										$db->Query();
										
									}
								}
							}
															
						}//end if id > 0
						
			    	}//end if orw

			    	$row++;	
			    }
			    
		 	}else{
		 		$mainframe->redirect('index.php?option=com_locator&task=admin&Itemid=' . JRequest::getInt('Itemid',''), JText::_('1: Import Failed!  File size: ' . $_FILES['userfile']['size'] . '.  User type: ' . $user->usertype . '. Upload: ' . $_POST['upload'] . 'Error:' . $_FILES['userfile']["error"]));	
		 	}
		 	fclose($handle);
		 	
		}else{
			$mainframe->redirect('index.php?option=com_locator&task=admin&Itemid=' . JRequest::getInt('Itemid',''), JText::_('2: Import Failed!  File size: ' . $_FILES['userfile']['size'] . '.  User type: ' . $user->usertype . '. Upload: ' . $_POST['upload'] . 'Error:' . $_FILES['userfile']["error"]));
		}
		
		$mainframe->redirect('index.php?option=com_locator&task=admin&Itemid=' . JRequest::getInt('Itemid',''), JText::_('Imported ' . $location_count . ' locations'));
				
	}
 	
	function _getListCount(){
		$menuitemid = JRequest::getInt( 'Itemid' );
		$starttime = "";
		$task = JRequest::getVar('task');
		$params = LocatorModelDirectory::getParams();
				
		$db = JFactory::getDBO();
		
		//always returns the number of search results without the limit
		$query = "SELECT FOUND_ROWS();";
		$db->setQuery( $query );
		
		$count = (int)$db->loadResult();
			
		$params = LocatorModelDirectory::getParams();
		$maxresults = (int)$params->get('maxresults',0);
		
		
		if($count > $maxresults && $maxresults > 0){
			
			if($task == "search_zip"){
					
				$count = $maxresults;	
			}
		}

		return $count;
	}
	
	
	function getActiveTags(){
		
		$c = new LocatorController();
		$db = JFactory::getDBO();
		
		$query = 'SELECT DISTINCT `id`, `name`,`tag_group`,`description`,`marker` FROM #__location_tags ORDER BY tag_group_order ,`order`,`name`';
		
		$db->setQuery( $query );
		$db->query();

		$tags = $db->loadObjectList();
		
		$tag_display = array();
		
		if(count($tags) > 0){
			
			$app = JFactory::getApplication();
			
			if($app->isSite()){
		
				$params = LocatorModelDirectory::getParams();
				$menu_tags = is_array($params->get('tags'))?($params->get('tags')):(array($params->get('tags')));
								
				if(count($menu_tags) > 0 && isset($menu_tags[0]) && $menu_tags[0] > 0){
					
					foreach ($menu_tags as $menu_tag){
						
						foreach ($tags as $tag_selected){
							
							if($tag_selected->id == $menu_tag){
								$tag_display[] = $tag_selected;
							}
						}
					}
					
				}else{
					$tag_display = $tags;
				}

			}else{
					$tag_display = $tags;
			}

		}
		
		return $tag_display;
					
	}
		
	function getTagList(){
		
		$selected = JRequest::getString('tags');

		$tag_display = LocatorModelDirectory::getActiveTags();

		$this->_lists['activetags'] = $tag_display;
			
		$tag_select[0]->id = "";
		
		$tag_select[0]->name = JText::_('LOCATOR_SELECT');
		
		$tag_display = array_merge($tag_select,$tag_display);
		
		if(count($tag_display) > 0){
			$this->_lists['tags'] = JHTML::_('select.genericlist',  $tag_display, 'tags', 'class="inputbox" size="1"', 'id', 'name',$selected);
			
		}
		
	}
		
	function getTagGroupLists(){
		
		
		$tag_display = LocatorModelDirectory::getActiveTags();
				
		$groups = array();
		
	
		//create a unique array of groups
		foreach ($tag_display as $tag){
			
			if(array_search($tag->tag_group,$groups) === false){

				$groups[] = $tag->tag_group;
				
			}
					
		}

		
		foreach ($groups as $group){
						
			foreach ($tag_display as $tag){
			
				if($tag->tag_group == $group){

					$tags_by_group[$group][] = array('id' => $tag->id, 'name' => $tag->name,'group' => $group);
					
				}
			}
		}
		
							
		$tag_select[0]->id = "";
		$tag_select[0]->name = JText::_('LOCATOR_SELECT');
					
		foreach ($groups as $group){

			$tags = JRequest::getVar('tags');
			
			$selected = (int)@$tags[$group];
						
			$html_group[]->name = $group;		
			
			$html_group[]->select = JHTML::_('select.genericlist', array_merge($tag_select,$tags_by_group[$group]) , 'tags['.$group.']', 'class="inputbox" size="1"', 'id', 'name',$selected);
		}
	
				
		$this->_lists['tag_groups']  = $html_group;
	
	}	
	
	
	function getTagGroupCheckBoxes(){
		
		$tag_display = LocatorModelDirectory::getActiveTags();
				
		$groups = array();
		
		//create a unique array of groups
		foreach ($tag_display as $tag){
			
			if(array_search($tag->tag_group,$groups) === false){

				$groups[] = $tag->tag_group;
				
			}
					
		}

		
		foreach ($groups as $group){
						
			foreach ($tag_display as $tag){
			
				if($tag->tag_group == $group){

					$tags_by_group[$group][] = array('id' => $tag->id, 'name' => $tag->name,'group' => $group);
					
				}
			}
		}
		
		$x = 0;
		$i = 0;
		$html_group = array();
		
		foreach ($groups as $group){

			$tags = JRequest::getVar('tags');

				
			
			$html_group[$x]->name = $group;		
			
			$html_group[$x]->checkboxes = array();
			
			foreach ($tags_by_group[$group] as $t){
			
				$checked = (@$tags[$t['id']] == @$t['id'])?(' checked="checked" '):('');
				
				$id = $group . '_' . $i;
				
				$html_group[$x]->checkboxes[$i] = '<label>' . $t['name'] . '';
				$html_group[$x]->checkboxes[$i] .= '<input '. $checked.' id="" type="checkbox" name="tags['.$t['id'].']" value="'.$t['id'].'" /></label>';
				
				$i++;
			}
						
			// = JHTML::_('select.genericlist',$tags_by_group[$group] , , 'class="inputbox" size="1"', 'id', 'name',$selected);
			// = JHTML::_('select.genericlist',$tags_by_group[$group] , 'tags['.$group.']', 'class="inputbox" size="1"', 'id', 'name',$selected);
			 $x++;
		}
	
				
		$this->_lists['tag_groups_checkboxes']  = $html_group;
			
	}

	/* Get the list if available provinces
	 * *
	 *
	 */
	function getProvinceList($in_module = false){
		
		$selected = JRequest::getString('states');

		$db = JFactory::getDBO();
		
		//if admin, show unpublished locations
		$clause = "";
		
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		if($app->isSite()){
			
			$clause = "AND l.published = 1";
			
			//filter if certain tags only
			
			//get the selected tags from the menu parameter
			$params = $this->getParams();
	  		$tagids = $params->get('tags');
		
	  	  	if(!is_array($tagids)){
	  	  		
	  	  		$tagids = (int)$tagids;
	  	  		
	  	  		if($tagids > 0){
					$clause .= " AND (fl.location_id IN (SELECT location_id FROM #__location_tags_link ll INNER JOIN #__location_tags ff ON ff.id = ll.tag_id WHERE ll.tag_id in (" . $tagids . ")))";	
	  	  		}
	  	  		
  			}else{
  				
  				$clause .= " AND (fl.location_id IN (SELECT location_id FROM #__location_tags_link ll INNER JOIN #__location_tags ff ON ff.id = ll.tag_id WHERE ll.tag_id in (" . implode(",",$tagids) . ")))";	
  			}
		}
		
		$query = 'SELECT DISTINCT `value` as `value`,`value` as label,1 as ordering FROM #__locations l 
					INNER JOIN #__location_fields_link fl ON fl.location_id = l.id
					INNER JOIN #__location_fields f ON f.id = fl.location_fields_id
					WHERE f.name = \'State\' '.$clause.'
					UNION ALL SELECT \'\' as `value`, \''.JText::_('LOCATOR_SELECT').'\' as `label`,0 AS ordering
					ORDER BY ordering,`value`';
	
		$db->setQuery( $query );
		$db->query();

		$states = $db->loadObjectList();	
		
		$onchange = '';
		
		$params = $this->getParams();
		
		if($app->isSite()){
			
			if($params->get('showcitydropdown',0) == 1 && $params->get('showstatedropdown',0) == 1){
					
				if($in_module == true){
					$function = 'module_filterCities();';
				}else{
					$function = 'filterCities();';					
				}

				$onchange = ' onchange="'.$function.'" ';
				$function = str_replace("();","",$function);
					
				$view = new LocatorViewDirectory();
			
				$view->initDOMLoadHook($params,$function);
			}
		}

		$this->_lists['states'] = JHTML::_('select.genericlist',  $states, 'states', 'class="inputbox" size="1" ' . $onchange, 'value', 'label',$selected);

		if($in_module == true){
			$this->_lists['states'] = str_replace('id="states"','id="module_states"',$this->_lists['states']);			
		}
		
	}
	
	
	/* Get the list if available countries *
	 * *
	 *
	 */
	function getCityList($in_module = false, $force = 0){
		
		$selected = JRequest::getString('city');

		$db = JFactory::getDBO();
		
		//if admin, show unpublished locations
		$clause = "";
		
		$app = JFactory::getApplication();

		if($app->isSite()){
			
			$clause = "AND l.published = 1";
			
			//get the selected tags from the menu parameter
			$params = $this->getParams();
	  		$tagids = $params->get('tags');
		
	  	  	if(!is_array($tagids)){
	  	  		
	  	  		$tagids = (int)$tagids;
	  	  		
	  	  		if($tagids > 0){
					$clause .= " AND (fl.location_id IN (SELECT location_id FROM #__location_tags_link ll INNER JOIN #__location_tags ff ON ff.id = ll.tag_id WHERE ll.tag_id in (" . $tagids . ")))";	
	  	  		}
	  	  		
  			}else{
  				
  				$clause .= " AND (fl.location_id IN (SELECT location_id FROM #__location_tags_link ll INNER JOIN #__location_tags ff ON ff.id = ll.tag_id WHERE ll.tag_id in (" . implode(",",$tagids) . ")))";	
  			}			
			
		}
		
		 //This query, selects both the state and the city
		 $query =  "SELECT DISTINCT 
			 CASE 
			 	WHEN fl2.value = '' or fl2.value is null 
			 	THEN CONCAT(':', fl.value ) 
			 		ELSE CONCAT( fl2.value, ':', fl.value ) 
			 	END as `value`
			 , 
			 CASE WHEN fl2.value = '' or fl2.value is null 
			 	THEN fl.value 
					ELSE CONCAT( fl.value, ', ', fl2.value )
				END as `label` 
			 ,1 AS ordering

					FROM #__locations l
					INNER JOIN #__location_fields_link fl ON fl.location_id = l.id
					INNER JOIN #__location_fields f ON f.id = fl.location_fields_id
					LEFT JOIN #__location_fields_link fl2 ON fl2.location_id = l.id AND fl2.location_fields_id=4
					WHERE 1
					$clause
					AND f.name =  'City'
					UNION ALL SELECT  '' AS  `value` ,  '" .JText::_('LOCATOR_SELECT')."' AS  `label` , 0 AS ordering
					ORDER BY ordering,  `value`";
		
				 
		 
		$db->setQuery( $query );
		$db->query();

		$states = $db->loadObjectList();	
			

		$this->_lists['city'] = JHTML::_('select.genericlist',  $states, 'city', 'class="inputbox" size="1"', 'value', 'label',$selected);		
		
		//TODO: less than ideal, but genericlist doesnt allow an ID/Name difference.
		if($in_module == true){
			$this->_lists['city'] = str_replace('id="city"','id="module_city"',$this->_lists['city']);			
		}
		
		
		$this->_lists['city_state'] = 'var city_state = new Array(';
		
		foreach($states as $state){
			$this->_lists['city_state'] .= ' \''. addslashes($state->value) . '\',';
		}
		
		$this->_lists['city_state'] = rtrim($this->_lists['city_state'],',');

		$this->_lists['city_state'] .= ');
		';
		
	}
	
	/* Get the list if available states and countries *
	 * *
	 *
	 */
	function getCityListByCountry($in_module = false, $force = 0){
		
		$selected = JRequest::getString('city');

		$db = JFactory::getDBO();
		
		//if admin, show unpublished locations
		$clause = "";
		
		$app = JFactory::getApplication();

		if($app->isSite()){
			
			$clause = "AND l.published = 1";
			
			//get the selected tags from the menu parameter
			$params = $this->getParams();
	  		$tagids = $params->get('tags');
		
	  	  	if(!is_array($tagids)){
	  	  		
	  	  		$tagids = (int)$tagids;
	  	  		
	  	  		if($tagids > 0){
					$clause .= " AND (fl.location_id IN (SELECT location_id FROM #__location_tags_link ll INNER JOIN #__location_tags ff ON ff.id = ll.tag_id WHERE ll.tag_id in (" . $tagids . ")))";	
	  	  		}
	  	  		
  			}else{
  				
  				$clause .= " AND (fl.location_id IN (SELECT location_id FROM #__location_tags_link ll INNER JOIN #__location_tags ff ON ff.id = ll.tag_id WHERE ll.tag_id in (" . implode(",",$tagids) . ")))";	
  			}			
			
		}
		
		 //This query, selects both the country and the city
		 $query =  "SELECT DISTINCT 
			 CASE 
			 	WHEN fl2.value = '' or fl2.value is null 
			 	THEN CONCAT(':', fl.value ) 
			 		ELSE CONCAT( fl2.value, ':', fl.value ) 
			 	END as `value`
			 , 
			 CASE WHEN fl2.value = '' or fl2.value is null 
			 	THEN fl.value 
					ELSE CONCAT( fl.value, ', ', fl2.value )
				END as `label` 
			 ,1 AS ordering

					FROM #__locations l
					INNER JOIN #__location_fields_link fl ON fl.location_id = l.id
					INNER JOIN #__location_fields f ON f.id = fl.location_fields_id
					LEFT JOIN #__location_fields_link fl2 ON fl2.location_id = l.id AND fl2.location_fields_id=8
					WHERE 1
					$clause
					AND f.name =  'City'
					UNION ALL SELECT  '' AS  `value` ,  '" .JText::_('LOCATOR_SELECT')."' AS  `label` , 0 AS ordering
					ORDER BY ordering,  `value`";
		//echo $query;
		//exit;
				 
		 
		$db->setQuery( $query );
		$db->query();

		$states = $db->loadObjectList();	
		
		//TODO: check if the list has US or blank in front, if not make it so (This is really bad)
			
		$this->_lists['city'] = JHTML::_('select.genericlist',  $states, 'city', 'class="inputbox" size="1"', 'value', 'label',$selected);		
	
		//TODO: less than ideal, but genericlist doesnt allow an ID/Name difference.
		if($in_module == true){
			$this->_lists['city'] = str_replace('id="city"','id="module_city"',$this->_lists['city']);			
		}
		
		$this->_lists['country_city'] = 'var country_city = new Array(';
		
		foreach($states as $state){
			$this->_lists['country_city'] .= ' \''. addslashes($state->value) . '\',';
		}
		
		$this->_lists['country_city'] = rtrim($this->_lists['country_city'],',');

		$this->_lists['country_city'] .= ');
		';
		
	}

	function showCityList($in_module = false){
											
			LocatorModelDirectory::getCityList($in_module);
			
			if(!isset($this->_lists['city_state'])){
				
				return;
			}
			
			//TODO: refactor this javascript, shouldn't be all over the place (possibly a single js asset)
			ob_start();
			
			$prefix = '';
								
			if($in_module == true){
				$prefix = "module_";
			}
				
			?>

			function <?php echo $prefix;?>filterCities(){
				<?php 
				
				echo $this->_lists['city_state'];
				
				?>
				var t = document.getElementById('<?php echo $prefix;?>states');
				var city_el = document.getElementById('<?php echo $prefix;?>city');
				var tmp = null;
				var offset = 0;
				var state = null;
				var cleared = false;
				var added = 0;
				
				if(city_el != null){
				
					//loop through the city:state array
					for(var x = 0; x < city_state.length; x++){

						tmp = city_state[x];
						offset = tmp.indexOf(":");
						
						//if the seperator is found
						if(offset > 0){
							
							//identify the state
							state = tmp.substr(0,offset);
							
							//if this state is the selected state, we have found at least one match and should proceed to clearing the city drop-down
							if(state == t.options[t.options.selectedIndex].value){
								
								//if we haven't already cleared it
								if(cleared == false){
									
									var y = 0;
									var removed = "";
						
									for (y = city_el.length; y > 0 ; y--){
										
										try{
										
											city_el.remove(y);
							
										}catch(e){}
										
								
									}
									
									cleared = true;
				
									//add the 'please select'
									city_el.options[added] = new Option('<?php echo JText::_('LOCATOR_SELECT')?>','');
									added++;
								}
															

								//add all cities from the array back.
								var opt = new Option();
								
								//plus one for the colon
								var city = tmp.substr(offset + 1,tmp.length);
								
								//add the option back to the list, text value
								city_el.options[added] = new Option(city + ", " + state,city_state[x]);
								added++;
							}
						}
					}
				}
			}
            
     
            
			
			<?php 
			
			$buffer = ob_get_contents();
			ob_end_clean();
			$doc = &JFactory::getDocument();
			
			$doc->addScriptDeclaration($buffer);

	}
	
	function showCityListByCountry($in_module = false){

			LocatorModelDirectory::getCityListByCountry($in_module);
			
			
			
			//TODO: refactor this javascript, shouldn't be all over the place (possibly a single js asset)
			ob_start();
			
			$prefix = '';
				
			?>

			
            
            <?php
				if(!isset($this->_lists['country_city'])){
					
					return;
				}
			?>
            
            function <?php echo $prefix;?>filterCitiesByCountry(){
				<?php 
				
				echo $this->_lists['country_city'];
				
				?>
				var t = document.getElementById('<?php echo $prefix;?>country');
				var city_el = document.getElementById('<?php echo $prefix;?>city');
				var tmp = null;
				var offset = 0;
				var country = null;
				var cleared = false;
				var added = 0;
				
				if(city_el != null){
				
					//loop through the country:city array
					for(var x = 0; x < country_city.length; x++){

						tmp = country_city[x];
						offset = tmp.indexOf(":");
						
						//if the seperator is found
						if(offset > 0){
							
							//identify the state
							country = tmp.substr(0,offset);
							
							//if this state is the selected state, we have found at least one match and should proceed to clearing the city drop-down
							if(country == t.options[t.options.selectedIndex].value){
								
								//if we haven't already cleared it
								if(cleared == false){
									
									var y = 0;
									var removed = "";
						
									for (y = city_el.length; y > 0 ; y--){
										
										try{
										
											city_el.remove(y);
							
										}catch(e){}
										
								
									}
									
									cleared = true;
				
									//add the 'please select'
									city_el.options[added] = new Option('<?php echo JText::_('LOCATOR_SELECT')?>','');
									added++;
								}
															

								//add all cities from the array back.
								var opt = new Option();
								
								//plus one for the colon
								var city = tmp.substr(offset + 1,tmp.length);
								
								//add the option back to the list, text value
								city_el.options[added] = new Option(city + ", " + country,country_city[x]);
								added++;
							}
						}
					}
				}
			}
			
			<?php 
			
			$buffer = ob_get_contents();
			ob_end_clean();
			$doc = &JFactory::getDocument();
			
			$doc->addScriptDeclaration($buffer);

	}
	
	/* Get the list if available countries
	 * *
	 *
	 */
	function getCountryList($in_module = false){
		
		$selected = JRequest::getString('country');

		$db = JFactory::getDBO();
		
		//if admin, show unpublished locations
		$clause = "";
		$onchange = '';
		
		$app = JFactory::getApplication();

		if($app->isSite()){
			
			$clause = "AND l.published = 1";
			
			//get the selected tags from the menu parameter
			$params = $this->getParams();
	  		$tagids = $params->get('tags');
		
	  	  	if(!is_array($tagids)){
	  	  		
	  	  		$tagids = (int)$tagids;
	  	  		
	  	  		if($tagids > 0){
					$clause .= " AND (fl.location_id IN (SELECT location_id FROM #__location_tags_link ll INNER JOIN #__location_tags ff ON ff.id = ll.tag_id WHERE ll.tag_id in (" . $tagids . ")))";	
	  	  		}
	  	  		
  			}else{
  				
  				$clause .= " AND (fl.location_id IN (SELECT location_id FROM #__location_tags_link ll INNER JOIN #__location_tags ff ON ff.id = ll.tag_id WHERE ll.tag_id in (" . implode(",",$tagids) . ")))";	
  			}
  			
		}
				
		$query = 	'SELECT DISTINCT fl.value as `value`,fl.value as label,1 as ordering FROM #__locations l 
				INNER JOIN #__location_fields_link fl ON fl.location_id = l.id
				INNER JOIN #__location_fields f ON f.id = fl.location_fields_id
				WHERE f.name = \'Country\' '.$clause.'
				UNION ALL SELECT \'\' as `value`, \''.JText::_('LOCATOR_SELECT').'\' as `label`,0 AS ordering
				ORDER BY ordering,`value`';
		
		//LEFT JOIN #__location_fields f2 ON (f2.id = fl.location_fields_id and  f2.name = \'TLD\' '.$clause.')
		//LEFT JOIN #__location_fields f2 ON (f2.id = fl.location_fields_id and  f2.name = \'TLD\' '.$clause.')
		
		//die(str_replace("#__","jos_",$query));

		$db->setQuery( $query );
		$db->query();

		$states = $db->loadObjectList();	
		
		if($app->isSite()){
			
			if($params->get('showcitydropdown',0) == 1 && $params->get('showcountrydropdown',0) == 1){
					
				if($in_module == true){
					$function = 'module_filterCitiesByCountry();';
				}else{
					$function = 'filterCitiesByCountry();';					
				}

				$onchange = ' onchange="'.$function.'" ';
				$function = str_replace("();","",$function);
					
				$view = new LocatorViewDirectory();
			
				$view->initDOMLoadHook($params,$function);
			}
		}
		
		$this->_lists['country'] = JHTML::_('select.genericlist',  $states, 'country', 'class="inputbox" size="1" ' .$onchange, 'value', 'label',$selected);		
		
		//TODO: less than ideal, but genericlist doesnt allow an ID/Name difference.
		if($in_module){
			$this->_lists['country'] = str_replace('id="country"','id="module_country"',$this->_lists['country']);			
		}
		
	}	

	function _getList($query,$limitstart = 0, $limit = 0){

		$db = JFactory::getDBO();
		$params =& $this->getParams();
				
		if($limit > 0){
			//die(str_replace("#__",$db->_table_prefix,$query));
			$db->setQuery( $query,$limitstart,$limit);
		}else{
			$db->setQuery( $query );
		}
		
		$rows = $db->loadObjectList();
		
		if ($db->getErrorNum()) {
			die($db->stderr());
			exit;
		}
	
		/*
		if($params->get('debug_mode',0) == 1){	
				  		
			if(!$rows){
			
				echo "<script> alert('".$db->getErrorMsg()."'); </script>";
				
			}
			
		}*/
			

		return $rows;
	}
		
	function getIPUserLocation(){

		$ip_address = $_SERVER['REMOTE_ADDR'];
		
		//$ip_address = gethostbyname($_SERVER['SERVER_NAME']);
	
		if(strpos($ip_address,"192.168") !== false){
			$ip_address = "75.71.24.155";
		}
		
		$parts = explode('.',$ip_address);
	
		$ip_number = ($parts[0] * 16777216) + (65536 * $parts[1]) + (256 * $parts[2]) + $parts[3];
		
		$db = JFactory::getDBO('force');
		
		$sql = 'SELECT latitude,longitude FROM #__location_iprange i 
				INNER JOIN #__location_iplocation l on i.ip_location_id = l.ip_location_id
				WHERE ' . $ip_number . '  >= start_ip AND ' . $ip_number . ' <= end_ip LIMIT 1';

		$db->setQuery($sql);
	
		$location = $db->loadObject();
		
		if($location){
			$res->lat = $location->latitude;
			$res->lng = $location->longitude;
		}
		
		return $res;
		
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
		
		$mainframe = JFactory::getApplication();
		$task  = JRequest::getString('task');
		$layout = JRequest::getString('layout','');
		
		$menuitemid = JRequest::getInt( 'Itemid' );
		$starttime = "";
		
		$params =& $this->getParams();
				
		if($params->get('debug_mode',0) == 1){	
				  		
			ini_set('display_errors', 1);
			ini_set('log_errors', 0);
			error_reporting(E_ALL);	
			
		}
		
		if(LocatorModelDirectory::hasAdmin() && $task==""){
				$task = "admin";	  	
		}

	  // Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			
			$query = $this->_buildQuery();
					
			$i = 0;		
			$j = 0;
			
			$maxresults = (int)$params->get('maxresults',0);
			$mainframe =& JFactory::getApplication();
									
			$limit = 	 (int)$this->getState('limit');
			$limitstart = (int)$this->getState('limitstart');
			
			$offset = (int)$limit + $limitstart;
						
			if($offset > $maxresults && $maxresults > 0 && $task == "search_zip"){
				
				if($limitstart > 0){
					$limit = $maxresults - $limit;
				}else{
					$limit = $maxresults;	
				}
			}

					
			if(strlen($query) > 0){
				
				$layout  = JRequest::getString('layout','');
				
				if($params->get('debug_mode',0) == 1){
				  	$mtime = microtime(); 
			        $mtime = explode(' ', $mtime); 
			        $mtime = $mtime[1] + $mtime[0]; 
			        $starttime = $mtime; 
			  	}
			  	
			  	if($params->get('set_big_selects',0) == 1){
		  			$db 		= JFactory::getDBO();
					$db->setQuery("SET SESSION SQL_BIG_SELECTS=1");
					$db->Query();
			  	}
		
			  	$Arows =& $this->_getList($query,$limitstart, $limit);
				
				$this->_data =& $Arows;

				if($params->get('debug_mode',0) == 1){	
				 	echo '<b>Debug: </b>ARows Reports  ' .  count($Arows) . ' results'; 
				}
          
			    if($params->get('debug_mode',0) == 1){		  		
					  $mtime = microtime(); 
			          $mtime = explode(" ", $mtime); 
			          $mtime = $mtime[1] + $mtime[0]; 
			          $endtime = $mtime; 
			          $totaltime = ($endtime - $starttime); 
			          echo 'This query was run in ' .$totaltime. ' seconds.'; 
				}				
				
			  	if($params->get('debug_mode',0) == 1){		  		
			  		          
					//echo "<pre>" . $layout . "" . str_replace("#__","jos_",$query . " LIMIT $newlimitstart, $newlimit ") . "</pre>";
			  	}	 
			}
			
		}
				
		
		if($params->get('debug_mode',0) == 1){  		
			$mtime = microtime(); 
			$mtime = explode(" ", $mtime); 
			$mtime = $mtime[1] + $mtime[0]; 
			$endtime = $mtime; 
			$totaltime = ($endtime - $starttime); 
			echo 'This page was created in ' .$totaltime. ' seconds.  Does not include query time.'; 
		}	
		
	return true;
	
	}
	
	
	
	/**
	 * Get the fields, type and values for a location
	 * TODO: ADD TAGLIST!
	 * @param unknown_type $id
	 */
	function &getFields($id){
		static $called = 0;
		
		$id = (int)$id;
		
		$sql = '
				SELECT name,type,value 
					FROM 
					#__location_fields f
				LEFT JOIN 
					#__location_fields_link l
 				ON l.location_fields_id = f.id and location_id = ' . $id . ' ORDER BY iscore DESC, `order` ASC';
		
		$db = JFactory::getDbo();
		$db->setQuery($sql);
		$fields = $db->loadObjectList();

		return $fields;	
	}
	
		
	/**
	 * Get the fields, type and values for a location
	 * TODO: ADD TAGLIST!
	 * @param unknown_type $id
	 */
	function &getTags($id){
		static $called = 0;
		

		$id = (int)$id;
		
		$sql = '
				SELECT name 

					FROM 

					#__location_tags f

				INNER JOIN 

					#__location_tags_link l

 				ON l.tag_id = f.id and l.location_id =' . $id . ' ORDER BY `order` ASC';
		
		$db = JFactory::getDbo();
		$db->setQuery($sql);
		$fields = $db->loadObjectList();

		return $fields;	
	}

	
	function setJSUserLocation(){

		if(file_exists( JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php')	){

			require_once( JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');
			CFactory::load( 'libraries' , 'userpoints' );
			CFactory::load( 'libraries' , 'window' );
			CFactory::load( 'helpers' , 'owner' );
			CFactory::load( 'libraries' , 'facebook' );
			
			CWindow::load();
			
			$document	=& JFactory::getDocument();
			$config		=& CFactory::getConfig();
			$script		= '/assets/script-1.2';
			$script		.= ( $config->get('usepackedjavascript') == 1 ) ? '.pack.js' : '.js';
			CAssets::attach( $script , 'js' );
			$my			= CFactory::getUser();
			
			//have they set their location to other than what's been detected at registration?
			$lat = JRequest::getVar('lat');
			$lng = JRequest::getVar('lng');
			$zoom = JRequest::getVar('zoom');

			
				$db = JFactory::getDBO();
								
				$sql = "UPDATE #__location_zips SET lat = $lat, lng = $lng WHERE location_id in (SELECT id from #__locations where user_id = " . $my->id . ")";
				$db->setQuery($sql);
	       		$db->Query();
	       		
				$sql = "UPDATE #__community_users SET latitude = $lat, longitude = $lng WHERE user_id = " . $my->id . "";
				$db->setQuery($sql);
	       		$db->Query();	       		
	       		
				$sql = "UPDATE #__community_fields_values SET value = '$lat' WHERE user_id = " . $my->id . " and field_id=26";
				$db->setQuery($sql);
	       		$db->Query();	   
	       		
	       		$sql = "UPDATE #__community_fields_values SET value = '$lng' WHERE user_id = " . $my->id . " and field_id=27";		
				$db->setQuery($sql);
	       		$db->Query();	      
	       		
	       		$sql = "UPDATE #__community_fields_values SET value = '$zoom' WHERE user_id = " . $my->id . " and field_id=28";		
				$db->setQuery($sql);
	       		$db->Query();		       		
	       		
		}
		
		//check if the user's lat/long was provided in a form
		if(strlen(JRequest::getString('user_lat','')) > 0){
			$res->lat = JRequest::getFloat('user_lat');
			$res->lng = JRequest::getFloat('user_lng');
		}		
		
	}
		
	function getJSUserLocation(&$res){

		if(file_exists( JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php')	){
			
			require_once( JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');
			CFactory::load( 'libraries' , 'userpoints' );
			CFactory::load( 'libraries' , 'window' );
			CFactory::load( 'helpers' , 'owner' );
			CFactory::load( 'libraries' , 'facebook' );
			
			CWindow::load();
			
			$document	=& JFactory::getDocument();
			$config		=& CFactory::getConfig();
			$script		= '/assets/script-1.2';
			$script		.= ( $config->get('usepackedjavascript') == 1 ) ? '.pack.js' : '.js';
			CAssets::attach( $script , 'js' );
			$my			= CFactory::getUser();
			
			//have they set their location to other than what's been detected at registration?
				
			if( $my->isOnline() && $my->id != 0 )
			{
				$db = JFactory::getDBO();
				
				$sql = "select lat,lng from 
						#__locations l
						inner join #__location_zips z on z.location_id=l.id
						where user_id=" . $my->id;
				
	       		$db->setQuery($sql);
	       		$res = $db->loadObject();
	       		
				$sql = "SELECT value from #__community_fields_values WHERE user_id = " . $my->id . " and field_id=28";		
			
				$db->setQuery($sql);
	       		$res->zoom = $db->loadResult();		       		
			       					
			}else{
				//TODO: autodetect a better default location based on the user's IP
				$db = JFactory::getDBO();
				
				$sql = "select lat,lng from 
						#__locations l
						inner join #__location_zips z on z.location_id=l.id
						where lat is not null LIMIT 1";
				
	       		$db->setQuery($sql);
	       		$res = $db->loadObject();
				
			}
		}
		
		//check if the user's lat/long was provided in a form
		if(strlen(JRequest::getString('user_lat','')) > 0){
			$res->lat = JRequest::getFloat('user_lat');
			$res->lng = JRequest::getFloat('user_lng');
		}
		
	}
	
	function getOverlays(){
		
		$db = JFactory::getDBO();
		$params 	= LocatorModelDirectory::getParams();	
		
		$regions = $params->get('overlay_regions');
		
		if(!is_array($regions)){
			$regions = array($regions);	
		}
		
		$region_list = implode(',',$regions);
	
		
		$sql = "SELECT NumGeometries(ogc_geom) as CNT,AsText(  ogc_geom ) as polygon,ISO2 as tld FROM #__location_world_boundaries WHERE REGION in ($region_list) ";
		
		$db->setQuery($sql);
		
		$rows = $db->loadObjectList();
		$this->_lists['polygons'] = array();
				
		if(count($rows))
		foreach ($rows as $row){

			if(!isset($this->_lists['polygons']["{$row->tld}"])){
				$this->_lists['polygons']["{$row->tld}"] = array();	
			}
			
			$multipolygons = explode('),(',$row->polygon);

			foreach ($multipolygons as $polygon){
				
				//order of find+replace is important
				$poly = str_replace(array(')','(','MULTIPOLYGON','POLYGON'),'',$polygon);
				
				$sets = explode(',',$poly);
				
				$points = array();
				
				//bring down the accuracy of the map by a factor of 10
				$maxpoints = 100;
				
				if((int)count($sets) > $maxpoints ){
					$mod = 30;
				}else{
					$mod = 1;	
				}
						
				$x = 0;
				
				foreach ($sets as $set){
					$x++;
					
					if($x % $mod == 0){
						
						$set = explode(' ',$set);
			
						array_push($points,array('lat' => floatval($set[1]),'lng' => floatval($set[0])));	
					}
				}
				
				//
				array_push($this->_lists['polygons']["{$row->tld}"],$points);
				
			}
			
		}
		
		//die('<pre>' . print_r($this->_lists['polygons'],1));
	
	}
	
	/**
	 * build the query
	 * @paramparam $count
	 */
	function _buildQuery($count = false)
	{
		$distance 		= '';
		$distance_field = '';
		$query			= '';
		
		$task 		= JRequest::getString('task','');	
		$zip  		= addslashes(JRequest::getString('postal_code',''));	
		$tags  		= JRequest::getVar('tags','');	
		$layout 	= JRequest::getString('layout','');
		$states  	= JRequest::getString('states','');
		$country  	= JRequest::getString('country','');
		$keyword 	= addslashes(JRequest::getString('keyword',''));
		$radius 	= JRequest::getFloat('radius',0);	
		$city  		= addslashes(JRequest::getString('city',''));
		$orderby 	= '';
		$menuitemid = JRequest::getInt( 'Itemid' );
		$params 	= LocatorModelDirectory::getParams();	
		$mainframe 	= JFactory::getApplication();
		$initial	= $params->get('initial_tags');

		
		//LEFTOFF
		if($params->get('show_overlays',0) == 1){
			$this->getOverlays();
		}
		
		
		
		//FRONT END
		if(!LocatorModelDirectory::hasAdmin()){
			
			//TODO: Clean this up.  We shouldnt have to update this every time we get a new search parameter
			if($params->get('showall') == 0 && $task== "" || ($params->get('showall') == 0 && $city == "" && $tags == "" && $country == "" && $states == "" && $zip == "" && $keyword =="" )){
				if($params->get('autofind',0) == 1 || strlen($initial) > 0){
					
				}else{
					
					return;
				}
			}
		}
		
		
		// Get the WHERE and ORDER BY clauses for the query
		$where	= $this->_buildDirectoryWhere();
												
		$distance = $this->_buildDirectoryDistance();
			  	
		if(strlen($distance) > 0){
			$orderby = " ORDER BY $distance,l.id";
		}else{
	  		$orderby = $this->_buildDirectoryOrderBy();		
		}
	  			
		
		//invalid postal code search
		if(strlen($distance) < 0){
			
			if($params->get('showall') == 0){

				return;		

			}
			
		}
		
		


	  	//always sort by distance from the requested postal code
		if(strlen($distance) > 0){
			
			if($params->get('autofind',0) == 1 && $task == ''){
				
				if($params->get('autofind_radius',0) > 0){
					
					$radius = $params->get('autofind_radius',0);
					
				}
			}
			
			$distance_field = ", $distance as Distance";
							
			if($params->get('force_match',0) == 1 && strlen($zip) > 0){
													
				$where .= " AND ($distance <= $radius or zip='$zip' or l.id IN (SELECT location_id FROM #__location_fields_link ll INNER JOIN #__location_fields ff ON ff.id = ll.location_fields_id WHERE LOWER(`value`) = '" . strtolower(trim($zip)) . "' AND `name`='PostalCode') )";

			}else{
				
				$where .= " AND $distance <= $radius";	
			}
			
			
		}
		
		$fields = 'l.id,l.description,l.published,l.name as title,z.zip,z.lat,z.lng ' . $distance_field . ' ';
		
		//get the selected tags from the menu parameter
	  	$tagids = $params->get('tags');
	  	
		$tag_where = '';
	
		if(!is_array($tagids)){
			if($tagids > 0){
				$tag_where = " AND t.id= " . (int)$tagids;
			}
		}else{
			$tag_where = " AND t.id in (" . implode(",",$tagids) . ")"; 
		}
  	
		//prioritize the selected tag.  (E.g. if they search for wine, show the wine icon)
		if(is_array($tags) && count($tags) > 0){
			
			$tag_where = " AND t.id in (";
			
			foreach ($tags as $key=>$val){
				
				if(strlen($val) > 0){
					$tag_where .= $val . ',';
				}
			}
			
			$tag_where = rtrim($tag_where,',') . ")";
			
		}elseif (strlen($tags) > 0){
			
			$tag_where = " AND t.id= " . (int)$tags;	
		}
			
	  	//get the top tag as the marker.  (We have to pick one!)
  		$tag_fields = ", (SELECT  
					marker
					FROM  
					#__location_tags_link tl 
					LEFT JOIN
					#__location_tags t ON t.id=tl.tag_id
					WHERE tl.location_id = l.id $tag_where
					ORDER BY `order` DESC LIMIT 1 ) AS marker,
					(SELECT  
					marker_shadow
					FROM 
					#__location_tags_link tl 
					LEFT JOIN
					#__location_tags t ON t.id=tl.tag_id
					WHERE tl.location_id = l.id $tag_where
					ORDER BY `order` DESC LIMIT 1 ) AS marker_shadow";		  
  		
  		$fields .= $tag_fields;
  		  				
		$tag_list_clause = '';
		
		//build the tag clause, ensuring to exclude tags not selected on this map
		$db = JFactory::getDBO();
		$db->setQuery('SELECT max(name) as `name`,`order` FROM #__location_tags WHERE `order` is not null group by `order` order by `order` ');
		$tag_rows = $db->loadObjectList();
		
		if(count($tag_rows) > 0){
			
			$tag_list_clause = ' ,(SELECT concat(';

			foreach ($tag_rows as $tag_row){
			
				$tag_row_order = (int)$tag_row->order;
				$tag_list_clause .= " max(case t.`order` when " . $tag_row_order . " then concat(t.`name`,',') else '' end)," ;
					
			}
			
			$tag_list_clause = rtrim($tag_list_clause,',');
			
			//get the selected tags from the menu parameter
		  	$tagids = $params->get('tags');
		  	
			$tag_where = '';
		
			if(!is_array($tagids)){
				if($tagids > 0){
					$tag_where = " AND t.id= " . (int)$tagids;
				}
			}else{
				$tag_where = " AND t.id in (" . implode(",",$tagids) . ")"; 
			}
  	
			
			//this Tag Where should only filter based on the menu item parameters, not the searched tags
			
			$tag_list_clause .= ') 
						FROM  
							#__location_tags_link tl 
						LEFT JOIN
							#__location_tags t 
						ON t.id=tl.tag_id
						WHERE 
						tl.location_id = l.id '.$tag_where.'
						) as taglist ';
		}
  		
  		if($params->get('showtaglist',0) == 1 || LocatorModelDirectory::hasAdmin()){

			$fields .= $tag_list_clause;
  		}

  		
  		$inner_where_clause = ' AND l.published = 1 ';
  		
  		if(JRequest::getVar('view') == 'default' || LocatorModelDirectory::hasAdmin()){
  			$inner_where_clause = '';
  		}

  		$db = JFactory::getDBO();
  		
		$query = 'SELECT 
					SQL_CALC_FOUND_ROWS 
					' . $fields .  '
			  FROM 
			  		#__locations l 
			  		LEFT JOIN #__location_zips z ON z.location_id = l.id
			  		
			  WHERE 1 ' . $inner_where_clause;
			
 
		$query .= $where . $orderby; 

		if(LocatorModelDirectory::hasAdmin()){
			
			//TODO: refactor this..get all lists
			$this->getProvinceList();
			$this->getTagList();
			$this->getCityList();
			$this->getCountryList();
		}
		
		$db = JFactory::getDBO();

		return $query;
	}


	/**
	 * build the query
	 * @param $count
	 */
	function _buildExportQuery($start = 0,$end = 100)
	{
		 $mainframe = JFactory::getApplication();
		
		//Name,Description,Published,Address,Address2,City,State,PostalCode,Phone,Date,Country,lat,lng,tag1,tag2
		$fields = 'l.id,a.name,l.description,l.published,z.lat,z.lng,l.name as title,i.value,location_fields_order';

  		$db= JFactory::getDBO();
  		$sql  = "SELECT * FROM #__location_tags ORDER BY `order`";
  		
  		$db->setQuery($sql);
		$tag_rows = $db->loadObjectList();	
		
		if(count($tag_rows) > 0){
			$fields .= ",";	
		}
		
		$x = 0;
		$i = 0;
		
	
		foreach ($tag_rows as $tag_row){
			$x++;
			$fields .= "(SELECT  
						name
						FROM 
						#__location_tags_link tl 
						LEFT JOIN
						#__location_tags t ON t.id=tl.tag_id
						WHERE tl.location_id = l.id 
						LIMIT $i,1) AS tag$x";
			$i++;
			if($x < count($tag_rows)){
				$fields .=",";
			}
		}	
		
		$query = 'SELECT SQL_CALC_FOUND_ROWS ' . $fields .  ' FROM 
				(SELECT #__location_fields.name,#__location_fields.id as location_fields_id,#__locations.id as location_id,#__location_fields.order as location_fields_order
					from #__locations ,
					#__location_fields
				order by #__locations.id,#__location_fields.order) as a
				LEFT JOIN #__location_fields_link i on i.location_id = a.location_id and i.location_fields_id = a.location_fields_id
				LEFT JOIN #__locations l on i.location_id = l.id
				LEFT JOIN #__location_zips z on z.location_id = a.location_id LIMIT '.$start . ',' . $end;

		return $query;
	}
	
	function _buildDirectoryOrderBy()
	{

        $mainframe = JFactory::getApplication();
        
        $params = LocatorModelDirectory::getParams();	
		  	      
    	$orderby = '';
    	
    	$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		if(!LocatorModelDirectory::hasAdmin()){

		  	$filter_order = strtolower($params->get('default_order','id'));
		  	
		  	//protect against 1.6 upgrade issues
		  	if($filter_order == "1"){
		  		$filter_order = "id";
		  	}
		  	
	        $filter_order_Dir = "ASC";		
			
		}else{
	    	
	        $filter_order     = $this->getState('filter_order');
	        $filter_order_Dir = $this->getState('filter_order_Dir');
		}
		
        if(!empty($filter_order) && !empty($filter_order_Dir) && $filter_order != "lng"){
        	$orderby = ' ORDER BY l.'. $filter_order. ' '  . $filter_order_Dir . ',l.id ASC' ;
        }else{
        	//default ordering by id, then field order
        	$orderby = 'ORDER BY l.id '  . $filter_order_Dir;
        }		
        
		return $orderby; 
		
	}
	/**
	 * Get the number of geocode request performed today
	 *
	 */
	function getGeocodeRequests(){
		
		$sql = "select 2500 - count(*) as remaining from #__location_zips where DATE(updated) = DATE(NOW())";
		$db = JFactory::getDBO();
		
		$db->setQuery($sql);
		
		$result = (int)$db->loadResult();
		
		return $result;
			
	}

	function checkPostalCode($zip){
		
		$zip = addslashes($zip);
		
		$sql = "SELECT lat,lng FROM #__location_zips WHERE zip='$zip' and lat is not null and lng is not null and lat != '' and lng != ''";
		$db = JFactory::getDBO();
		
		$db->setQuery($sql);
		
		$row = $db->loadObject();
		//die(print_r($db));
		if(strlen(@$row->lat) > 0){
			return $row;
		}
		
		return false;	
	}
	
	/**
	 * Returns the distance formula clause used in the where and select statements
	 *
	 */
	function _buildDirectoryDistance(){
		
		$task = JRequest::getString('task','');	
		$menuitemid = JRequest::getInt( 'Itemid' );
		$clause = "";	
		$params = LocatorModelDirectory::getParams();
	 	$distance_unit = $params->get('distance_unit','LOCATOR_M');
	 			
		//get the lat/long of the zip code of interest
		$zip = addslashes(JRequest::getVar('postal_code',''));
						  
		if(strlen($zip) > 0){
			
			$db = JFactory::getDBO();
			
			$sql = 'SELECT lat,lng FROM #__location_zips WHERE zip=\'' . $zip . '\'';
			
			$db->setQuery($sql);
			
			$row = $db->loadObject();
			
		}
		
		//if we found the location by IP address
		if($params->get('autofind',0) == 1 && !$row){
			
			//$p = JProfiler::getInstance();

			$row = LocatorModelDirectory::getIPUserLocation();
		
		}
		
		//if we found location from the device
		if($params->get('autofind',0) == 1 && JRequest::getInt('device_location',0) == 1){
			
			//check if the user's lat/long was provided in a form
			if(strlen(JRequest::getString('user_lat','')) > 0){
				$row->lat = JRequest::getFloat('user_lat');
				$row->lng = JRequest::getFloat('user_lng');
			}
			
		}
	
		if(@strlen($row->lng) > 0){
			
			if($distance_unit == 'LOCATOR_M'){
				$clause = "3959 * ACOS(SIN(RADIANS({$row->lat})) * SIN(RADIANS(lat)) + COS(RADIANS({$row->lat})) * COS(RADIANS(lat)) * COS(RADIANS(lng) - RADIANS({$row->lng})))" ;
			}else{
				$clause = "6371 * ACOS(SIN(RADIANS({$row->lat})) * SIN(RADIANS(lat)) + COS(RADIANS({$row->lat})) * COS(RADIANS(lat)) * COS(RADIANS(lng) - RADIANS({$row->lng})))" ;
			}
				
		}
					
		
		return $clause;
	}
	
	function getName(){
		
		return "directory";	
	}

	function _buildDirectoryWhere()
	{
		$mainframe = JFactory::getApplication();

		$task 		= JRequest::getString('task','');		
		$zip  		= JRequest::getString('postal_code','');		
		$layout  	= JRequest::getString('layout','');
		
		$states  	= addslashes(JRequest::getString('states',''));
		$country  	= addslashes(JRequest::getString('country',''));
		
		$keyword 	= addslashes(JRequest::getString('keyword',''));
		$city  		= addslashes(JRequest::getString('city',''));
		
		$published	= JRequest::getInt('published',-1);
		$geocoded	= JRequest::getInt('geocoded',-1);
		
		//city has the state prepended
		if(strpos($city,':') !== false){
			$city = substr($city,strpos($city,':') + 1,strlen($city));
		}
		
		$user		=& JFactory::getUser();
		$gid		= (int)$user->get('aid', 0);
		$params 	= LocatorModelDirectory::getParams();
		$cmd 		= JRequest::getString('cmd','');	
		$clause		= '';
		$keywords	= array();
		$where = '  ';
		
		if(strlen($keyword) > 0){			
			
			
			//check if it's wrapped in quotes
			if(strpos(stripslashes(trim($keyword)),'"') === 0 && strpos(stripslashes(trim($keyword)),'"',1) === strlen(stripslashes(trim($keyword))) - 1){
		
				$keyword = str_replace('"','',trim($keyword));
				
				$keyword = addslashes($keyword);
				
				$clause =  $keyword;		
				
			}else{
				
				$keywords = explode(" ",$keyword);
	
				$clause =  (implode("%' OR '%", $keywords));
				
			}
			
			$where .= " AND (
							l.id IN (
							SELECT l.id from #__locations l WHERE 
								LOWER(l.name) like '%" . strtolower(trim($clause)) . "%'
								OR LOWER(l.description) like '%" . strtolower(trim($clause)) . "%'
								OR l.id IN (SELECT location_id FROM #__location_fields_link WHERE LOWER(`value`) LIKE '%" . strtolower(trim($clause)) . "%')
							)
						)
						 ";
		}

		
		if(strlen($states) > 0){
			
			$where .= " AND (
							l.id IN (SELECT location_id FROM #__location_fields_link ll INNER JOIN #__location_fields ff ON ff.id = ll.location_fields_id WHERE LOWER(`value`) = '" . strtolower(trim($states)) . "' AND `name`='State')
						)
						 ";			
			
		}
		
		if(strlen($city) > 0){
			
			$where .= " AND (
							l.id IN (SELECT location_id FROM #__location_fields_link ll INNER JOIN #__location_fields ff ON ff.id = ll.location_fields_id WHERE LOWER(`value`) = '" . strtolower(trim($city)) . "' AND `name`='City')
						)
						 ";			
			
		}		
		if(strlen($country) > 0){
			
			$where .= " AND (
							l.id IN (SELECT location_id FROM #__location_fields_link ll INNER JOIN #__location_fields ff ON ff.id = ll.location_fields_id WHERE LOWER(`value`) = '" . strtolower(trim($country)) . "' AND `name`='Country')
						)
						 ";				
		}		
		
		if($published >= 0){
			
			$where .= " AND published = $published ";
										
		}	
		
		if($geocoded >= 0){
			
			if($geocoded == 1){
				$where .= " AND lat is not null ";
			}else{
				$where .= " AND (lat is null or lat = '') ";
			}
										
		}	
		
		//searching for tags
		if(is_array(JRequest::getVar('tags')) === true){

			$unique = array();
			
			$temp = array_unique(JRequest::getVar('tags'));
			
			while (list($index,$data)=each($temp)) {
			      if ((int)$data > 0) {
			          $unique[$index]=$data;
			      }
			}

			if(count($unique) > 0){
			$clause = '(' . implode(',',$unique) . ')';	
				
				if(strlen($clause) > 0){
				
					$where .= " AND (l.id IN (SELECT location_id FROM #__location_tags_link ll INNER JOIN #__location_tags ff ON ff.id = ll.tag_id WHERE ll.tag_id IN " . $clause . "))";			
				}
			}

		}else{
			
			$tags = addslashes(JRequest::getString('tags',''));
			
			if(strlen(strtolower(trim($tags))) > 0){
				
				$where .= " AND (l.id IN (SELECT location_id FROM #__location_tags_link ll INNER JOIN #__location_tags ff ON ff.id = ll.tag_id WHERE ll.tag_id = '" . strtolower(trim($tags)) . "'))";			
				
			}
		}

		//dont include items without a lat/lng on the map
		//NB: This allows for ungeocoded entries to be displayed in directory/search view
		if($layout == "gmap" || $layout == "combined" || $layout == "mobile"){
			$where .= " AND (l.id IN (SELECT location_id FROM #__location_zips ll WHERE ll.lat is not null))";
		}
		  
	  	//get the selected tags from the menu parameter
	  	$tagids = $params->get('tags');
	  	
	  	//if we have initial tags selected, they should override the tags parameter
	  	if($task != 'search_zip'){
	  		if($params->get('initial_tags')){
	  			$tagids = $params->get('initial_tags');
	  		}
	  	}
			  	
  	  	if(!is_array($tagids)){
  	  		
  	  		$tagids = (int)$tagids;
  	  		
  	  		if($tagids > 0){
				$where .= " AND (l.id IN (SELECT location_id FROM #__location_tags_link ll INNER JOIN #__location_tags ff ON ff.id = ll.tag_id WHERE ll.tag_id in (" . $tagids . ")))";	
  	  		}
  	  		
		}else{
			
			$where .= " AND (l.id IN (SELECT location_id FROM #__location_tags_link ll INNER JOIN #__location_tags ff ON ff.id = ll.tag_id WHERE ll.tag_id in (" . implode(",",$tagids) . ")))";	
		}
	
  			
  		//show only the chosen countries (from the menu parameter)
  		$countries = $params->get('country');
			
  	  	if(!is_array($countries)){
  	  			  	  		
  	  		if(strlen(trim($countries)) > 0){
  	  			
				$where .= " AND (
							l.id IN (SELECT location_id FROM #__location_fields_link ll INNER JOIN #__location_fields ff ON ff.id = ll.location_fields_id WHERE LOWER(`value`) = '" . strtolower(trim($countries)) . "' AND `name`='Country')
						)";	
  	  		}
	  		
		}else{
			
				$where .= " AND (
						l.id IN (SELECT location_id FROM #__location_fields_link ll INNER JOIN #__location_fields ff ON ff.id = ll.location_fields_id WHERE LOWER(`value`) in ('" . implode("','",$countries) . "') AND `name`='Country')
					)";	
		
		}
	
		return $where;
	}
	
}