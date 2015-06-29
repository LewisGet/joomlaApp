 <?php 
 // no direct access
 defined('_JEXEC') or die('Restricted access');
 jimport('joomla.html.toolbar');
 
 class LocatorHelperToolbar extends JObject
 {        
 	
 	var $bar;
 	
 		function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true, $x = false)
		{
			$this->bar = & JToolBar::getInstance('Locator');
	
			//strip extension
			$icon	= preg_replace('#\.[^.]*$#', '', $icon);
	
			// Add a standard button
			$this->bar->appendButton( 'Standard', $icon, $alt, $task, $listSelect, $x );
		}
		
		/**
		* Writes a common 'publish' button
		* @param string An override for the task
		* @param string An override for the alt text
		* @since 1.0
		*/
		function publish($task = 'publish', $alt = 'Publish')
		{
			$this->bar = & JToolBar::getInstance('Locator');
			// Add a publish button
			//$this->bar->appendButton( 'Publish', false, $alt, $task );
			$this->bar->appendButton( 'Standard', 'publish', $alt, $task, false, false );
		}
	
		/**
		* Writes a common 'publish' button for a list of records
		* @param string An override for the task
		* @param string An override for the alt text
		* @since 1.0
		*/
		function publishList($task = 'publish', $alt = 'Publish')
		{
			$this->bar = & JToolBar::getInstance('Locator');
			// Add a publish button (list)
			$this->bar->appendButton( 'Standard', 'publish', $alt, $task, true, false );
		}
		
		
	/**
	* Writes a common 'unpublish' button
	* @param string An override for the task
	* @param string An override for the alt text
	* @since 1.0
	*/
	function unpublish($task = 'unpublish', $alt = 'Unpublish')
	{
		$this->bar = & JToolBar::getInstance('Locator');
		// Add an unpublish button
		$this->bar->appendButton( 'Standard', 'unpublish', $alt, $task, false, false );
	}

	/**
	* Writes a common 'unpublish' button for a list of records
	* @param string An override for the task
	* @param string An override for the alt text
	* @since 1.0
	*/
	function unpublishList($task = 'unpublish', $alt = 'Unpublish')
	{
		$this->bar = & JToolBar::getInstance('Locator');
		// Add an unpublish button (list)

		$this->bar->appendButton( 'Standard', 'unpublish', $alt, $task, true, false );
	}		
		
	function addNewX($task = 'add', $alt = 'New')
	{
		$this->bar = & JToolBar::getInstance('Locator');
		// Add a new button (hide menu)
		$this->bar->appendButton( 'Standard', 'new', $alt, $task, false, true );
	}
	
	/**
	* Write a trash button that will move items to Trash Manager
	* @since 1.0
	*/
	function trash($task = 'remove', $alt = 'Trash', $check = true)
	{
		$this->bar = & JToolBar::getInstance('Locator');
		// Add a trash button
		$this->bar->appendButton( 'Standard', 'trash', $alt, $task, $check, false );
	}
	
	function preferences($component, $height='150', $width='570', $alt = 'Preferences', $path = '')
	{
		$user =& JFactory::getUser();

		$component	= urlencode( $component );
		$path		= urlencode( $path );
		$this->bar = & JToolBar::getInstance('Locator');
		// Add a configuration button
		$this->bar->appendButton( 'Popup', 'config', $alt, 'index.php?option=com_config&amp;controller=component&amp;component='.$component.'&amp;path='.$path, $width, $height );
	}	

	/**
	* Writes a save button for a given option
	* Apply operation leads to a save action only (does not leave edit mode)
	* @param string An override for the task
	* @param string An override for the alt text
	* @since 1.0
	*/
	function apply($task = 'apply', $alt = 'Apply')
	{
		$this->bar = & JToolBar::getInstance('Locator');
		// Add an apply button
		$this->bar->appendButton( 'Standard', 'apply', $alt, $task, false, false );
	}

	/**
	* Writes a save button for a given option
	* Save operation leads to a save and then close action
	* @param string An override for the task
	* @param string An override for the alt text
	* @since 1.0
	*/
	function save($task = 'save', $alt = 'Save')
	{
		$this->bar = & JToolBar::getInstance('Locator');
		// Add a save button
		$this->bar->appendButton( 'Standard', 'save', $alt, $task, false, false );
	}
			
       function getToolbar() {
 
                $this->bar = new JToolBar( 'Locator' );

                $task = JRequest::getVar('task','');
              
                $view = JRequest::getVar('view','directory');
                                
                if(JRequest::getVar('format') == 'raw'){
                	return;	
                }
                
                if($task == 'export'){
                	return;	
                }
                if($task == 'checkpostalcode'){
                	return;	
                }
                
                switch ($view){
                	
                	case 'directory':{ 
                		
		                switch($task){
		                	
		                	case 'view':{
		                 		?><h2>My Locators</h2><?php
				                $this->bar->appendButton( 'Standard', 'new', 'New Locator', 'newItem', false );
				                $this->bar->appendButton( 'Separator' );
				                $this->bar->appendButton( 'Standard', 'delete', 'Delete Locator', 'delete', false );
		                	}break;
		                	
		                	case 'add':
		                	case 'edit':{
		                		?><h2>Add &amp; Edit Location</h2><?php
		                        $this->bar->appendButton( 'Standard', 'save', 'Save', 'save', false );
				                $this->bar->appendButton( 'Separator' );
				                $this->bar->appendButton('custom', '<a class="toolbar" href="index.php?option=com_locator&view=directory&Itemid='.JRequest::getInt('Itemid').'" title="Cancel"><span title="Cancel" class="icon-32-cancel"></span>Cancel</a>','Cancel','cancel',false);			
		                	}break;
		                	       	
		                	case 'addtag':
                			case 'edittag':{
                				?><h2>Tag Editor</h2><?php
		                		$this->bar->appendButton( 'Standard', 'save', 'Save', 'save', false );
				               $this->bar->appendButton('custom', '<a class="toolbar" href="index.php?option=com_locator&view=directory&Itemid='.JRequest::getInt('Itemid').'" title="Cancel"><span title="Cancel" class="icon-32-back"></span>Back</a>','Cancel','cancel',false);	
                			}break;
                			
                			case 'addfield':
                			case 'editfield':{
                				?><h2>Field Editor</h2><?php
		                		$this->bar->appendButton( 'Standard', 'save', 'Save', 'save', false );
				               	$this->bar->appendButton('custom', '<a class="toolbar" href="index.php?option=com_locator&view=directory&Itemid='.JRequest::getInt('Itemid').'" title="Cancel"><span title="Cancel" class="icon-32-back"></span>Back</a>','Cancel','cancel',false);	
                			}break;
                			
		                	case 'tag':{
		                		?><h2>Tag</h2><?php
		                        $this->bar->appendButton( 'Standard', 'save', 'Save', 'save', false );
				                $this->bar->appendButton( 'Separator' );
				                $this->bar->appendButton('custom', '<a class="toolbar" href="index.php?option=com_locator&view=directory&Itemid='.JRequest::getInt('Itemid').'" title="Cancel"><span title="Cancel" class="icon-32-cancel"></span>Cancel</a>','Cancel','cancel',false);	
		               
		                	}break;
		                	
		                	case 'import':{
		                		?><h2>Location Import</h2><?php
		                        $this->bar->appendButton( 'Standard', 'save', 'Save', 'import_upload', false );
				                $this->bar->appendButton('custom', '<a class="toolbar" href="index.php?option=com_locator&view=directory&Itemid='.JRequest::getInt('Itemid').'" title="Cancel"><span title="Cancel" class="icon-32-cancel"></span>Cancel</a>','Cancel','cancel',false);	
		                				
		                		  		
		                	}break;
		                	
		                	case 'geocode':{
		                		?><h2>Bulk Geocoder</h2><?php
				                $this->bar->appendButton('custom', '<a class="toolbar" href="javascript:void(0);" onclick="javascript:geocode();" title="Geocode"><span title="Geocode" class="icon-32-html"></span>Geocode</a>','Geocode','cancel',false);	       
				                $this->bar->appendButton('custom', '<a class="toolbar" href="javascript:void(0);" onclick="javascript:clearcache();" title="Clear Cache"><span title="Clear Cache" class="icon-32-html"></span>Clear Cache</a>','ClearCache','cancel',false);
				                $this->bar->appendButton('custom', '<a class="toolbar" href="index.php?option=com_locator&view=directory&Itemid='.JRequest::getInt('Itemid').'" title="Cancel"><span title="Cancel" class="icon-32-back"></span>Back</a>','Cancel','cancel',false);	
		                			 		
		                	}break;
		                	
		                	case 'showimportcsv':{
		                
		                	}break;
		                	
		                	case 'managetags':{
	                			?><h2>My Tags</h2><?php
		                		$this->bar->appendButton( 'Standard', 'new', 'New', 'addtag', false );
				                $this->bar->appendButton( 'Standard', 'delete', 'Delete', 'removetags', false );
				                $this->bar->appendButton('custom', '<a class="toolbar" href="index.php?option=com_locator&view=directory&Itemid='.JRequest::getInt('Itemid').'" title="Cancel"><span title="Cancel" class="icon-32-back"></span>Back</a>','Cancel','cancel',false);	
	                	 	}break;
	                	 	
	                	 	case 'managefields':{
		                		?><h2>Fields</h2><?php
		               			$this->bar->appendButton( 'Standard', 'new', 'New', 'addfield', false );
				                $this->bar->appendButton( 'Standard', 'delete', 'Delete', 'removefields', false );
				                $this->bar->appendButton('custom', '<a class="toolbar" href="index.php?option=com_locator&view=directory&Itemid='.JRequest::getInt('Itemid').'" title="Cancel"><span title="Cancel" class="icon-32-back"></span>Back</a>','Cancel','cancel',false);	
	                   
		                	}break;
		                	
		                	default:{
		   						?><h2>My Locations</h2><?php
								LocatorHelperToolbar::publishList();
								LocatorHelperToolbar::unpublishList();
								
								//LocatorHelperToolbar::custom( 'managetags', 'copy.png', 'copy_f2.png',JText::_( 'Manage Tags' ),false );
								LocatorHelperToolbar::custom( 'tag', 'default.png', 'default_f2.png', JText::_( 'Tag' ),true );
								//LocatorHelperToolbar::custom( 'managefields', 'copy.png', 'copy_f2.png',JText::_( 'Manage Fields' ),false );		
		
								//$this->bar->custom( 'managefields', 'copy.png', 'copy_f2.png',JText::_( 'Manage Fields' ),false );		
								//LocatorHelperToolbar::custom( 'import', 'default.png', 'default_f2.png', JText::_( 'Import' ),false );
								//LocatorHelperToolbar::custom( 'export', 'default.png', 'default_f2.png', JText::_( 'Export' ),false );
										
								LocatorHelperToolbar::custom( 'geocode', 'default.png', 'default_f2.png', JText::_( 'Geocode' ),false );
								
								LocatorHelperToolbar::trash();
								//$this->bar->editListX();
								LocatorHelperToolbar::addNewX();
								//LocatorHelperToolbar::preferences('com_locator', '550');
					
						        
		                	}break;
		           
		                }
		                
                	}break;
                	
                	case 'tags':{
                	
                		switch($task){
                			
                			case 'addtag':
                			case 'edittag':{
                				?><h2>Tag Editor</h2><?php
		                		$this->bar->appendButton( 'Standard', 'save', 'Save', 'save', false );
				               $this->bar->appendButton('custom', '<a class="toolbar" href="index.php?option=com_locator&view=directory&Itemid='.JRequest::getInt('Itemid').'" title="Cancel"><span title="Cancel" class="icon-32-back"></span>Back</a>','Cancel','cancel',false);	
                			}break;
                			
                				
	                		default:{
	                			?><h2>My Tags</h2><?php
		                		$this->bar->appendButton( 'Standard', 'new', 'New', 'addtag', false );
				                $this->bar->appendButton( 'Standard', 'delete', 'Delete', 'removetags', false );
				                $this->bar->appendButton('custom', '<a class="toolbar" href="index.php?option=com_locator&view=directory&Itemid='.JRequest::getInt('Itemid').'" title="Cancel"><span title="Cancel" class="icon-32-back"></span>Back</a>','Cancel','cancel',false);	
	                	 	}break;
                		} 
                		
                	}break;
                	
                	
                	case 'fields':{
                			
                		switch($task){
                			
                			case 'addfield':
                			case 'editfield':{
                				?><h2>Field Editor</h2><?php
		                		$this->bar->appendButton( 'Standard', 'save', 'Save', 'save', false );
				               	$this->bar->appendButton('custom', '<a class="toolbar" href="index.php?option=com_locator&view=directory&Itemid='.JRequest::getInt('Itemid').'" title="Cancel"><span title="Cancel" class="icon-32-back"></span>Back</a>','Cancel','cancel',false);	
                			}break;
                			
                			default:
                				
		                	case 'fields':{
		                		?><h2>Fields</h2><?php
		               			$this->bar->appendButton( 'Standard', 'new', 'New', 'addfield', false );
				                $this->bar->appendButton( 'Standard', 'delete', 'Delete', 'removefields', false );
				                $this->bar->appendButton('custom', '<a class="toolbar" href="index.php?option=com_locator&view=directory&Itemid='.JRequest::getInt('Itemid').'" title="Cancel"><span title="Cancel" class="icon-32-back"></span>Back</a>','Cancel','cancel',false);	
	                   
		                	}break;
                		}
                		
                	}break;
                	
                }

                return $this->bar->render();
 
        }
 
 }
 
 ?>
