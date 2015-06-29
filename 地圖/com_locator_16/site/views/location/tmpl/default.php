<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: default.php 951 2011-11-12 16:24:45Z fatica $
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_COMPONENT_SITE . DS . 'views' . DS . 'directory' . DS . 'view.html.php');

	$view = new LocatorViewDirectory();

	$item = $this->items[0];

	if((int)$item->id <= 0){
		JError::raiseError('404',JText::_('NOT_FOUND'));
	}
		
	$params = &JComponentHelper::getParams( 'com_locator' );
	
	$menuitemid = JRequest::getInt( 'Itemid' );
	 	
	if ($menuitemid)
	{
		$menu = JSite::getMenu();
		$menuparams = $menu->getParams( $menuitemid );
		$params->merge( $menuparams );
	}
	
	
	$gmap_width 		= $params->get( 'item_gmap_width','420');
	$gmap_height 		= $params->get( 'item_gmap_height','400');
	
	$doc =& JFactory::getDocument();
	$doc->addStyleSheet( 'components/com_locator/assets/locator.css' );
	
	$view->initDOMLoadHook($params);
	
	$doc->addScriptDeclaration("jqLocator(document).ready(initialize);");
	
	$doc =& JFactory::getDocument();
	
	if(JRequest::getString('layout','') == 'mobile'){
		$doc->addStyleSheet( 'components/com_locator/assets/locator_mobile.css' );
	}else{
		$doc->addStyleSheet( 'components/com_locator/assets/locator.css' );
	}
	
	//ML
	$external_css = $params->get('external_css','');
	$external_font = $params->get('external_font','');
	$cssref = $params->get('cssref','');
	$doc =& JFactory::getDocument();
	
	if(strlen($external_css) > 0){	
		$doc->addStyleSheet( '/components/com_locator/assets/css/' . $external_css);
	}
	
	if(strlen($cssref) > 0){	
		$doc->addStyleSheet($cssref);
	}
	
	if(strlen($external_font) > 0){	
		$doc->addStyleDeclaration( 'body{	font-family:' . $external_font . ';}');
	}	

	if($params->get('showmaponitempage',1) == 1){
				
		$language = $params->get('gmap_language','en-GB');
		
		if($params->get('use_ssl',0) == 1){
			$doc->addScript("https://maps-api-ssl.google.com/maps/api/js?sensor=false&language=$language");
		}else{
			$doc->addScript("http://maps.google.com/maps/api/js?sensor=false&language=$language");
		}

		//hook the init function to the window's onload event.
				
		//these are used by Mapinit
		$gmap_default_lat = (string)$item->lat;
		$gmap_default_lng = (string)$item->lng;
		$defaultmapview 	= $params->get( 'defaultmapview','ROADMAP' );
		$gmap_default_zoom 	= $params->get( 'item_gmap_default_zoom',10 );
		
		//ensure we dont end up with multiple links to nested pages
		$params->set( 'linktoitempage',0 );
		
		ob_start();
		
		$path = JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'directory' . DS . 'mapinit.php';
		
		require_once($path);
		
		?>
		
		var openWindow = 0;

		<?php if(strlen($item->lat) > 0){ ?>
			
				var myLatlng = new google.maps.LatLng(<?php echo (string)$item->lat; ?>,<?php echo (string)$item->lng; ?>);
				var point = myLatlng;
				var contentString = '<div class="popupWindow">';
				contentString += '<?php echo str_replace("'","\'",$view->formatItem($item,$params,'item_map_address_template')); ?>';
			
				<?php if ( $params->get( 'show_directions', 1 ) == 1){ ?>
				//add the driving directions code
			
				contentString += '<a href="javascript:void(0);" onclick="showDirections(0,<?php echo (string)$item->lat; ?>,<?php echo (string)$item->lng; ?>);" ><?php echo JText::_('LOCATOR_GET_DIRECTIONS'); ?></a><div id="0_directions" style="height:30px;"></div><br />';
				
				<?php } ?>			
			
				contentString += '</div>';
				
			    infoWindow = new google.maps.InfoWindow({
			        content: contentString
			    });

				<?php 
				
					$icon = "";
					$shadow = "";
					
					if(isset($item->marker)){
						if(strlen($item->marker) > 0){
						?> 
							myIcon = "<?php echo $item->marker; ?>";				
							<?php if (strlen($item->marker_shadow) > 0){ ?>
							
							myShadow = "<?php echo $item->marker_shadow; ?>";
							
							<?php 
							$shadow = "shadow:myShadow,";
				
							}
								
							$icon = "icon:myIcon,";
						}
					}
				?>
				 marker = new google.maps.Marker({
						position: myLatlng, 
					 	map: map,
					 	<?php echo $icon; ?>
					 	<?php echo $shadow; ?>
					 	title:'<?php echo $item->title; ?>'
				});
				
				google.maps.event.addListener(marker, 'click', function() {
			 	
		      		infoWindow.open(map,marker);
		      	
		      		openWindow = 1;
			      	
			    });
			    
			    <?php  if($params->get('showmarkeronitempage',0) == 1) {  ?>
			        infoWindow.open(map,marker);
			   <?php  }  ?>
			<?php
			}
			?>
} //end javascript initialize
			<?php
		
		$buffer = ob_get_contents();
		ob_end_clean();
		$doc->addScriptDeclaration($buffer);
	}

	?>
	<div class="com_locator_entry single">	
		<div class="locator_text">
			<div class="locator_back"><a href="javascript:history.go(-1);"><?php echo JText::_('Back'); ?></a></div>
			
			<div class="address">
			<?php
				
			$format = $view->formatItem($item,$params,'item_address_template');
			
			if($params->get('triggercontentplugin',0) == 1){
				$format = JHTML::_('content.prepare', $format);
			}
				
			$doc->setTitle(utf8_encode(html_entity_decode($item->title)));

			//output the item template 
			?>
			<div class="com_locator_address">
				<?php
				
			
					echo $format;	
				?>		
			</div>			
		</div>
		</div>
	<div id="locator_map_canvas" class="locator_item_map" style="width:<?php echo $gmap_width; ?>px; height:<?php echo $gmap_height; ?>px;"></div>	
	<div id="locator_map_directions" class="locator_combined_directions"></div>
</div><!-- end com_locator entry single -->	