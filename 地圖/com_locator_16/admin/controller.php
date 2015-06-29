<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$ 
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');


// Build the path to the model based upon a supplied base path
require_once(JPATH_SITE.DS.'components'.DS.'com_locator'.DS.'models'.DS.'directory.php');


class LocatorController extends JController{
	
	/**
	* Edits a location
	* 	
	* @access	public
	* @since	1.5
	*/
	function tag()
	{		
		
		if(LocatorModelDirectory::hasAdmin() === true){
			
			JRequest::setVar('view', 'location' );
			
			JRequest::setVar('layout','multiple');
			
			parent::display();
			
		}else{
			
			JError::raiseError(403,JText::_('ACCESS DENIED'));	
			
		}
	}	

		
	function checkPostalCode(){
		// Get/Create the model
		$model = & $this->getModel('directory');
		
		if($model->checkPostalCode(JRequest::getString('postal_code'))){
			echo "1";
		}else{
			echo "0";
		}
		
	}
	
	function showimportcsv(){
		
		if(LocatorModelDirectory::hasAdmin() === true){
			$model = & $this->getModel('directory');
			$model->exportLocations(true);
		}else{
			
			JError::raiseError(403,JText::_('ACCESS DENIED'));	
			
		}
		
	}	

	function geocode()
	{

		JRequest::setVar('view', 'directory' );
		
		JRequest::setVar('layout','geocode');
				
		parent::display();
	}	
	

	
	function showsearch()
	{

		JRequest::setVar('view', 'search' );
		
		JRequest::setVar('layout','search');
				
		parent::display();
	}	
		
	
	function utils()
	{

		JRequest::setVar('view', 'directory' );
		
		JRequest::setVar('layout','utils');
				
		parent::display();
	}	
	
		

	function clearcache()
	{

		if(LocatorModelDirectory::hasAdmin() === true){
			$model = $this->getModel('directory');
			
			$model->clearCache();
			die();	
		}else{
			
			JError::raiseError(403,JText::_('ACCESS DENIED'));	
			
		}
	}	
		
	
		
	function export(){
		
		if(LocatorModelDirectory::hasAdmin() === true){
			JRequest::setVar('format','raw');
	
			$model = $this->getModel('directory');
			$model->exportLocations();
			die();
		}else{
			
			JError::raiseError(403,JText::_('ACCESS DENIED'));	
			
		}
			
	}
	
	
	function savegeocode(){
		
		$model = $this->getModel('location');
		
		$model->savegeocode();		
		
	}
	
	function savefield(){
		
		if(LocatorModelDirectory::hasAdmin() === true){
			$model = $this->getModel('fields');
		
			$model->save();
			
			$mainframe = JFactory::getApplication();
			$mainframe->redirect("index.php?option=com_locator&task=managefields&Itemid=" . JRequest::getInt('Itemid',''),"Field saved");
		}else{
			
			JError::raiseError(403,JText::_('ACCESS DENIED'));	
			
		}
		
	}	
	function import_upload()
	{
		if(LocatorModelDirectory::hasAdmin() === true){
			$model = $this->getModel('directory');
			
			$model->importUpload();
		}else{
			
			JError::raiseError(403,JText::_('ACCESS DENIED'));	
			
		}
	}	
	
	
	/**
	* Edits a location
	* 	
	* @access	public
	* @since	1.5
	*/
	function import()
	{
		if(LocatorModelDirectory::hasAdmin() === true){
			$user	=& JFactory::getUser();
				
			JRequest::setVar('view', 'directory' );
			
			JRequest::setVar('layout','import');
			parent::display();
		}else{
			
			JError::raiseError(403,JText::_('ACCESS DENIED'));	
			
		}
		
	}	

	
	
	function savemultiple(){
		
		if(LocatorModelDirectory::hasAdmin() === true){
			
			$model = $this->getModel('location');
			
			$model->saveMultiple();
		}else{
			
			JError::raiseError(403,JText::_('ACCESS DENIED'));	
			
		}
	}
	
	/**
	* Edits a location
	* 	
	* @access	public
	* @since	1.5
	*/
	function edit()
	{
		
		if(LocatorModelDirectory::hasAdmin() === true){
			$user	=& JFactory::getUser();
				
			JRequest::setVar('view', 'location' );
			
			JRequest::setVar('layout','form');
			parent::display();
		}else{
			
			JError::raiseError(403,JText::_('ACCESS DENIED'));	
			
		}
	}
	
	/**
	* Edits a location
	* 	
	* @access	public
	* @since	1.5
	*/
	function edittag()
	{
		if(LocatorModelDirectory::hasAdmin() === true){
			$user	=& JFactory::getUser();
				
			JRequest::setVar('view', 'tags' );
			
			JRequest::setVar('layout','form');
			parent::display();
		
		}else{
			
			JError::raiseError(403,JText::_('ACCESS DENIED'));	
			
		}
		
	}
	
	
	
	/**
	* Edits a location
	* 	
	* @access	public
	* @since	1.5
	*/
	function addtag()
	{
			
		if(LocatorModelDirectory::hasAdmin() === true){
			
			$user	=& JFactory::getUser();
				
			JRequest::setVar('view', 'tags' );
						
			JRequest::setVar('layout','form');
			parent::display();
			
		}else{
			
			JError::raiseError(403,JText::_('ACCESS DENIED'));	
			
		}
	}	

	
	function editfield()
	{
		if(LocatorModelDirectory::hasAdmin() === true){
			
			$user	=& JFactory::getUser();
				
			JRequest::setVar('view', 'fields' );
			
			JRequest::setVar('layout','form');
			
			parent::display();
			
		}else{
			
			JError::raiseError(403,JText::_('ACCESS DENIED'));	
			
		}
	}
	

	function addfield()
	{
		if(LocatorModelDirectory::hasAdmin() === true){
			$user	=& JFactory::getUser();
				
			JRequest::setVar('view', 'fields' );
						
			JRequest::setVar('layout','form');
			parent::display();
		}
	}	
			
		
	
	/**
	* Edits a location
	* 	
	* @access	public
	* @since	1.5
	*/
	function add()
	{
		
		$user	=& JFactory::getUser();
			
		JRequest::setVar('view', 'location' );
			
		JRequest::setVar('layout','form');
		parent::display();
	}	
	
	/* Save the posted location 
	 * *
	 *
	 */
	function save(){
				
		$model = $this->getModel('location');
		
		$model->save();
	}
	
	/* Save the posted location 
	 * *
	 *
	 */
	function savetag(){
		
		$model = $this->getModel('tags');
		
		
		$model->save();
						
		$mainframe = JFactory::getApplication();
		
		$mainframe->redirect('index.php?option=com_locator&task=managetags&Itemid=' . JRequest::getInt('Itemid',''),JText::_('Tag Saved'));
	}
		
	
	/* Save the posted location 
	 * *
	 *
	 */
	function removetags(){
		if(LocatorModelDirectory::hasAdmin() === true){
		$model = $this->getModel('tags');
		
		$model->remove();
		}
	}
		
	function removefields(){
		
		if(LocatorModelDirectory::hasAdmin() === true){
			$model = $this->getModel('fields');
			
			$model->remove();
		}
	}
			

	function set_userfield(){
		
		if(LocatorModelDirectory::hasAdmin() === true){
			
			$model = $this->getModel('fields');
			
			$model->set_userfield();
			
		}
		
	}
		
	function unset_userfield(){
		
		if(LocatorModelDirectory::hasAdmin() === true){
			
			$model = $this->getModel('fields');
			
			$model->unset_userfield();
			
		}
		
	}
	
	function unpublish(){

		if(LocatorModelDirectory::hasAdmin() === true){
		$model = $this->getModel('location');
		
		$model->unpublish();
		}
		
	}
	
	function remove(){

		if(LocatorModelDirectory::hasAdmin() === true){
		$model = $this->getModel('location');
		
		$model->remove();
		}
		
	}
		
	
	function publish(){

		if(LocatorModelDirectory::hasAdmin() === true){
		$model = $this->getModel('location');
		
		$model->publish();
		}
		
	}
	
	function managetags(){
		
		if(LocatorModelDirectory::hasAdmin() === true){
		JRequest::setVar('view', 'tags' );
		JRequest::setVar('layout','default');
		
		
		$model = $this->getModel('tags');
		
		parent::display();
		}

	}
	
	function managefields(){
		
		if(LocatorModelDirectory::hasAdmin() === true){
		JRequest::setVar('layout','default');
		JRequest::setVar('view', 'fields' );
		
		$model = $this->getModel('fields');
		
		parent::display();
		}

	}	
	
	/**
	 * Display a single location or a directory, depending on whether an id was passed
	 *
	 */
	function display(){
		
	
		$app = JFactory::getApplication();
		
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			
			$default = JRequest::getInt('id') ? 'location' : 'directory';
			
			JRequest::setVar('view', $default );
			
		}		
		

		$params = &JComponentHelper::getParams( 'com_locator' );
		$menuitemid = JRequest::getInt( 'Itemid' );
		
		if ($menuitemid)
		{
			$menu = JSite::getMenu();
			$menuparams = $menu->getParams( $menuitemid );
			$params->merge( $menuparams );
		}

		if($app->isSite()){
			parent::display(true);
		}else{
			parent::display();		
		}
 
	}
	
}

?>