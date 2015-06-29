<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: controller.php 961 2011-11-26 07:58:43Z fatica $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class LocatorController extends JController{
	
	/**
	* Edits a location
	* 	
	* @access	public
	* @since	1.5
	*/
	function edit()
	{
		$user	=& JFactory::getUser();

		// Create the view
		$view = & $this->getView('location', 'html');

		// Get/Create the model
		$model = & $this->getModel('Form');

		// Push the model into the view (as default)
		$view->setModel($model, true);

		// Set the layout
		$view->setLayout('form');

		// Display the view
		$view->display();
	}

	function savegeocode(){
		
		$model = $this->getModel('location');
		
		$model->savegeocode();		
		
	}	
	
	/**
	 * Display a single location or a directory, depending on whether an id was passed
	 *test
	 */
	function display(){
	
			  	
		JHTML::_('behavior.caption');
		
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			
			$default	= JRequest::getInt('id') ? 'location' : 'directory';
			
			JRequest::setVar('view', $default );
			
			$layout = JRequest::getString('layout','default');
			
			JRequest::setVar('layout',$layout);
		}

		$model = &$this->getModel('directory');
			
		$model->getJSUserLocation($res);
		
		$component = JComponentHelper::getComponent( 'com_locator' );
		$params = &JComponentHelper::getParams( 'com_locator' );
		
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
		  		
	
		if(isset($res->lat)){
			
			if(abs($res->lat) > 0){
			
				$params->set('gmap_default_lat',@$res->lat);
				$params->set('gmap_default_lng',@$res->lng);
				$params->set('gmap_default_zoom',@$res->zoom);
		
			}
		}
		
				
		//parent::display();
		
	}
	
	function javascript(){
		
		$component = JComponentHelper::getComponent( 'com_locator' );
		$params = &JComponentHelper::getParams( 'com_locator' );
		
		
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
				
		$maxresults = 			(int)$params->get('maxresults',0);
		$gmap_default_lat = 	$params->get( 'gmap_default_lat',41.397);
		$gmap_default_lng = 	$params->get( 'gmap_default_lng',-96.644 );
		$gmap_default_zoom = 	$params->get( 'gmap_default_zoom',4 );
		$gmap_width = 			$params->get( 'gmap_width','550');
		$gmap_height = 			$params->get( 'gmap_height','500');
		$distance_unit_label = 	JText::_($params->get( 'distance_unit','LOCATOR_M'));
		$defaultmapview = 		$params->get( 'defaultmapview','ROADMAP' );
		$showemptymap = 		$params->get( 'showemptymap','0');		
				
		$doc = JFactory::getDocument();
		
		$path = JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'directory' . DS . 'mapinit.php';
				
		require_once($path);
			
		for ($i = 0; $i < count($this->items);  $i++){
			
			$item = $this->items[$i];
		
			$j = $i + 1;
			
			for ($i = 0; $i < count($this->items);  $i++){
				
				$item = $this->items[$i];
		
				$this->item =& $item;
				$this->params =&$params;
				$this->index = $i;
				
				if($item->id > 0 && strlen($item->lat) > 0){		
					
					echo $this->loadTemplate('map_item');
					
				}
			}
		}
		
		showCenterOnResults($params);
		
		?>
		}  //end init function
		<?php	

	}
	
	function saveuserlocation(){
		// Get/Create the model
		$model = & $this->getModel('directory');
		
		$model->setJSUserLocation();		
	}
	

	
	/* Save the posted location 
	 * *
	 *
	 */
	function save(){
		
		
		$model = $this->getModel('location');
	
		$model->save();
		
		//JRequest::setVar('view','directory' );
	}
	
}

?>
