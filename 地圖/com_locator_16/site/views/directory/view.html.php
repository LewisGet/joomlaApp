<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: view.html.php 1008 2012-06-06 20:45:53Z fatica $
 */


// Check to ensure this file is included in Joomla! 
defined('_JEXEC') or die( 'Restricted access' );

if(file_exists(JPATH_BASE . DS . 'components' . DS . 'com_locator' . DS . 'view.php')){
	require_once (JPATH_BASE . DS . 'components' . DS . 'com_locator' . DS . 'view.php');
}


/**
 * HTML Article View class for the Content component
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class LocatorViewDirectory extends LocatorView 
{

	/**
	 * This function shows the "No results" in the case a search was performed.
	 * It prevents the display of that message if there are no results, but a search was not performed
	 *
	 */
	function showNoResults(){
			
		if(JRequest::getVar('task') == "search_zip" && (strlen(JRequest::getVar('postal_code')) > 0 || 
		(is_array(JRequest::getVar('tags')) && count(JRequest::getVar('tags')) > 0)  || strlen(JRequest::getVar('tags')) > 0 || strlen(JRequest::getVar('keyword')) > 0)){
		?>
			<h4><?php echo JText::_('LOCATOR_NO_RESULTS'); ?></h4>
		<?php
		}
		
	}
	
	function initDOMLoadHook($params,$function = ''){
						
		$doc =& JFactory::getDocument();
		
		if(strlen($function) > 0){
			
			$doc->addScriptDeclaration( 'jqLocator(document).ready('.$function.');' );
			
		}

		$doc->addScript( JURI::base() . 'components/com_locator/assets/jquery.min.js' );
		$doc->addScript(  JURI::base() . 'components/com_locator/assets/jquery.noconflict.js' );	
		
		if(strlen($params->get('google_analytics')) > 0){
		
			$ua = $params->get('google_analytics','');
			
			if(strpos($ua,'UA') !== false && strpos($ua,'-') !== false && strlen($ua) > 5){
						
				$script = "
				
				  var _gaq = _gaq || [];
				  _gaq.push(['_setAccount', '".$ua."']);
				  _gaq.push(['_trackPageview']);
				
				  (function() {
				    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
				  })();
				
				";
				
				$doc->addScriptDeclaration($script);
			
			}
				
		}

		
	}
	
	function showPagination(&$params){
				
		//echo "<!-- {$this->total} ". $params->get('list_length',500)." -->";
		
		if($this->total > $params->get('list_length',500)){
			?><div id="locator_pagination">
			<?php
				echo JText::_('Display') . $this->showLimitBox($params);
				echo $this->pagination->getPagesLinks();
			?></div><?php
		}
		
	}
	
	function showArticle(&$params){
	?>
		<div class="com_locator_article">
		<?php
		//TODO: there's probably a better way to do this, but...
		$article_id  = (int)$params->get('article',0);
		
		if($article_id >0){
			
			$db= JFactory::getDBO();
			$sql = 'SELECT `introtext`,`fulltext` FROM #__content WHERE id=' . $article_id;
			$db->setQuery($sql);
			$object = $db->loadObject();
			
			if(isset($object)){
				echo $object->introtext . $object->fulltext;
			}
			
		}

		?>
		</div>
		<?php			
	}

	
	function showLimitBox(&$params){
		
		global $mainframe;

		// Initialize variables
		$limits = array ();

		// Make the option list
		
		$page_list = $params->get('page_list','50,100,250,500');
		
		$pages = explode(",",$page_list);
		
		foreach ($pages as $p){
			
			$limits[] = JHTML::_('select.option', $p);	
		}
		
		$c = new JConfig();
				
		$selected = JRequest::getInt('limit',$params->get('page_list_default',100));
			
		$html = JHTML::_('select.genericlist',  $limits, 'limit', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', $selected);
		
		return $html;	
	}
	
	function formatItem(&$item,&$params,$template,$no_html = 0){

			$app = JFactory::getApplication();
			$doc =& JFactory::getDocument();
						
			$default_format = '{title}{distance}[br]{address}[br]{address2}[br] {city}, {state} {postalcode}[br]{phone}[br]{link}[br]{description}[br]{taglist}';
			
			$task = JRequest::getString('task');
	
			//TODO: update to new admin check
			if($app->isAdmin() || (JRequest::getVar('view') == 'directory' && JRequest::getVar('layout','default') == 'default') ){
				$default_format = '{address} {address2} {city}, {state} {postalcode}';	
			}
			
			if($no_html == 1){
				$format = $template;
			}else{
				$format = $params->get($template,$default_format);
			}

			$format = str_replace('[/','</',$format);
			$format = str_replace('[','<',$format);		
			$format = str_replace(']','/>',$format);
			
			//split the description into introtext/fulltext if needed
			if(strpos($item->description,'<hr id="system-readmore"') !== false){
				$item->introtext = substr($item->description,0,strpos($item->description,'<hr id="system-readmore"'));
				$item->fulltext = substr($item->description,strpos($item->description,'<hr id="system-readmore />"'),strlen($item->description));
			}

			//allow for showing only the introtext
			if(strpos($item->description,'<hr id="system-readmore"') !== false && $params->get('showintroonly',0) == 1){
				$item->description = substr($item->description,0,strpos($item->description,'<hr id="system-readmore"'));
			}
						
			static $index = 0;
			
			//die(print_r($item->fields));
			if(!isset($item->fields)){
				
				//$model = $this->getModel('directory');
				
				$item->fields =& LocatorModelDirectory::getFields($item->id);
				
				//die(print_r($item));
				 //$model->getFields();
			
			}
			
			if(count($item->fields) > 0){
				
				//declare the item type variables
				foreach ($item->fields as $field){  
					
					//Old: $field->name = strtolower(str_replace(' ','',$field->name));
					$field->name = preg_replace('/[^a-zA-Z0-9\s]/', '', str_replace(" ","",strtolower($field->name)));
					
					if(isset($field->value)){
						$field->value = str_replace(array("\n","\r"),'',$field->value);
					}else{
						$field->value = '';
					}
					
					//$field->value = htmlentities($field->value, ENT_QUOTES, 'UTF-8');

					$value 		= '';
					$link_value = '';
							
					switch (strtolower($field->type)){
							
						case "tld":{
								
						}break;
						
						case "meta":{
							
							if(JRequest::getVar('view') == 'location'){
								if(strlen(trim($field->value)) > 0){
									$value = $doc->getMetaData( str_replace('meta','',$field->name), $field->value );
									$doc->setMetaData( str_replace('meta','',$field->name), $field->value . $value );
								}
							}
							
						}break;
											
						case "email":{
							
							if($no_html == 1){
								$value = $field->value;
							}else{
								$value = '<span class="line_item email">' . '<a href="mailto:' . htmlentities($field->value) . '">'. htmlentities($field->value) .'</a>' . '</span>';
							}
							
						}break;
						
						case "image":{
							
							if($no_html == 1){
								$value = $field->value;
							}else{
								$value = '<span class="line_item image">' . '<img src="' . $field->value .'" alt="" />' . '</span>';
							}
							
						}break;
						
						case "twitter":{
							
							if(strpos($format, '{twitter}') !== false){

								$index = $index + 1;
								$timeout = $index * 1000;
								$doc = JFactory::getDocument();
								
								$value = $field->value;
					
								//check if we have a tweet at a different location
								$sql = "SELECT * FROM `tweets` WHERE screen_name = '{$field->value}' order by created_at DESC LIMIT 1";
								
								$db = JFactory::getDBO();
								
								$db->setQuery($sql);
								
								$result = $db->loadObject();
								
								if($result){
									
									//update the description with the latest Tweets!
									$sql = "UPDATE #__locations SET description = '{$result->text} {$result->created_at}' WHERE id = {$item->id}";
									$db->setQuery($sql);
									$db->Query();
									
									//update the location if needed
									if(abs($result->lat) > 0){
										$doc->addScriptDeclaration('
										 
											jqLocator(document).ready(function(){
												var user = \''.$field->value.'\';
												setTimeout(function(){getTweet(user,'.$index.','.$item->id.')},'.$timeout.');
											});
										
										');
									}
								}
							
							}
											
						}break;
						
						case "html":{
							$value = $field->value;
							
						}break;
							
						case "link":{
								
							$target = '';
							
							
							if($no_html == 1){
								
								$value = $field->value;
								
							}else{
												
								if(strlen(trim($field->value)) > 0){
									
									if($params->get('linktarget',1) == 1){
										$target = ' target="_blank"';
									}
									
									if($params->get('linkhttp',0) == 1){
										
										if(!preg_match('/^http/',$field->value)){
											
											$field->value = 'http://' . $field->value;
										}
										
									}
		
									$value = '<span class="line_item '.$field->name.'">' . '<a href="' . htmlentities($field->value) . '" '.$target.'>';
																
									//we have a nested tag {link {title} /link}
									if(strpos($format,'{' . $field->name . '}') === false && strpos($format,"{".$field->name."") !== false){
										
										$format = str_replace('{'. $field->name, $value,$format);	
										
										$value = '</a>' . '</span>';
										
										$format =  str_replace('/'.$field->name.'}', $value,$format);	
																	
									}else{
										$value .= htmlentities($field->value) .'</a>' . '</span>';
									}
									
								}else{
									
									//clean up link tags, with or without a br
									$format = preg_replace('/{'.$field->name.'.+\/'.$field->name.'}<br\/>/', '',$format);	
									$format = preg_replace('/{'.$field->name.'.+\/'.$field->name.'}/', '',$format);	
					
									$value = '';
								}
							}
			
						}break;
												
						default:{
							if($no_html == 1){
								$value = $field->value;
							}else{
								$link_value = urlencode($field->value);
								$value 		= '<span class="line_item '.$field->name.'">' . $field->value . '</span>';
							}
						}break;
							
					}

					$replace 		= '/{' . $field->name . '}/';
					//$link_format 	= preg_replace($replace, $link_value ,$link_format);
					
					if(trim($field->value) == ''){
						$replace = '/{' . $field->name . '}<br\/>/';
					}
					
					$format = preg_replace($replace,$value,$format);	
					
					if(trim($field->value) == ''){
						$replace = '/{' . $field->name . '}/';
					}
					
					$format = preg_replace($replace, $value,$format);	
						
					
				}

				
			}
			

			//Lat and lng
			$format = str_replace('{lat}',@$item->lat,$format);		
			$format = str_replace('{lng}',@$item->lng,$format);		
			$format = str_replace('{published}',@$item->published,$format);		
			
			$value = '';
			
			if (strlen(@$item->Distance) > 0){
				
				$distance_unit_label = JText::_($this->params->get( 'distance_unit','LOCATOR_M'));
				
				if(JRequest::getVar('task') == "search_zip" && (strlen(JRequest::getVar('postal_code')) > 0 || strlen(JRequest::getVar('user_lat')) > 0 )){
					
						$from_label = JRequest::getString('postal_code');
						
						if(strlen(JRequest::getVar('user_lat')) > 0){
							$from_label	= JText::_('LOCATOR_YOUR_LOCATION');
						}
					
						$value .= '<span class="com_locator_distance">';
						$value .= JText::_('LOCATOR_APPROXIMATELY') . ' ' . (int)@$item->Distance . ' ' . $distance_unit_label . ' ' . JText::_('LOCATOR_MILES_FROM') . ' ' . $from_label;
						$value .= '</span>';
				}
				
				
			}		
			
			//TODO: change to faster REGEX 
			if($value == ''){
				$format = str_replace('{distance}<br/>',$value,$format);
				$format = str_replace('{distance}',$value,$format);
			}else{
				$format = str_replace('{distance}',$value,$format);		
			}
					

			$value = '';
			
			if($params->get('showtaglist',0) == 1){
	
				if(isset($item->taglist)){			
					if(strlen($item->taglist)){  
						$value .= '<div class="com_locator_taglist">';
						$value .= rtrim($item->taglist,", "); 
						$value .= '</div>';
					}
				}
			}
			
			$format = str_replace('{taglist}',$value,$format);	
			
			//INSERT THE description into the template
			$value = '';	

			if(strlen($item->description)){ 
				if($no_html == 1){
					$value = str_replace("\r\n","",utf8_decode( utf8_encode ( $item->description)));
				}else{
					$value = '<div class="com_locator_description">'. $item->description . '</div>';
				}
			}
								
			$format = str_replace('{description}',$value,$format);	
						
			//insert the FULLTEXT into the description
			$value = '';	
			
			if($no_html == 1){
				$value = $item->fulltext;
			}else{
				if(strlen(@$item->fulltext)){ 
					$value = '<div class="com_locator_fulltext">'. $item->fulltext . '</div>';
				}
			}
								
			$format = str_replace('{fulltext}',$value,$format);	
			
			
			//insert the INTROTEXT into the description
			$value = '';	

			if(strlen(@$item->introtext)){ 
				$value = '<div class="com_locator_introtext">'. $item->introtext . '</div>';
			}
								
			$format = str_replace('{introtext}',$value,$format);	
			
			$value = '';
			
			

			if(strlen($item->title)){
				
				$layout = JRequest::getString('layout');
				$menuitemid = JRequest::getInt( 'Itemid' );
				$linktoitempage = $params->get( 'linktoitempage',1 );
				$itempagelayout = 'default';
										
						
				if($linktoitempage == 1){
					
					if($layout == 'mobile'){
						$itempagelayout = 'mobile';
					}
					
					$href= 'index.php?option=com_locator&view=location&layout='.$itempagelayout.'&id=' . $item->id . '&Itemid=' . $menuitemid;	
					
					if(JRequest::getString('tmpl') == 'component'){
						$href.='&tmpl=component';	
					}			
					
					$link = '<a href="'. JRoute::_($href) .'">'.stripslashes($item->title).'</a>';			
				}else{
					
					$link = stripslashes($item->title);	
					
				}
				
				//always link combined to the map marker
				if($layout == "combined" && $template == 'address_template' ){
					$j = $this->index;
					$link = '<a href="javascript:openMarkerWindow(' . $j . ');">' . stripslashes($item->title) . '</a>';
				}
				
				$value = '<h2 class="com_locator_title">'. $link . '</h2>';
	
			}
			
			if($no_html == 1){
				$value = stripslashes($item->title);			
			}
								
			$format = str_replace('{title}',$value,$format);	
			
			$value  = stripslashes($item->title);
			
			$format = str_replace('{name}',$value,$format);

			//remove newlines		
			$format = str_replace(array("\n","\r","\x1a"),"",$format);
			
			//remove control characters
			$format = preg_replace('/[\x00-\x1F\x7F]/', '',$format);
			
			return $format;	
	}
	
	function display($tpl = null){
		
		//ML
		$model = $this->getModel('directory');
		if($model->hasAdmin()){
		
			$this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'directory' . DS . 'tmpl');
		}

		$task = JRequest::getString('task','');		
		$zip  = JRequest::getString('postal_code','');	
		
		$total = 0;
		$items = null;
		
		// Get some data from the model
		$items		= & $this->get( 'Data');
		$total		= & $this->get( 'Total');		
	
		$remaining 	= & $this->get('GeocodeRequests');
		
		$state =& $this->get( 'state' );

		if($task == "geocode"){	
			$model->geocode();
		}
		
		$model->getTagList(false);	
		$lists		= & $this->get( 'Lists');
		@$lists['geocode'] = $model->_lists['geocode'];
		
		
		//assign template variables
		$lists['order_Dir'] = $state->get( 'filter_order_Dir' );
        $lists['order']     = $state->get( 'filter_order' );
        
		$this->items =& $items;
	
		jimport('joomla.html.pagination');
		
		$pagination = new JPagination($total, $state->get('limitstart'), $state->get('limit'));	
	
		
		$this->assignRef('remaining',$remaining);
		$this->assignRef('lists',$lists);
		$this->assign('total',			$total);
		$this->assignRef('items',$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);
		
		parent::display($tpl);
	
	}
	
}