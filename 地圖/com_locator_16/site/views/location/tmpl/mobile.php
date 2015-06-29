<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: default.php 729 2011-04-06 19:33:25Z fatica $
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
	$doc =& JFactory::getDocument();
	
	$language = $params->get('gmap_language','en-GB');
	
	
	if($params->get('use_ssl',0) == 1){
		$doc->addScript("https://maps-api-ssl.google.com/maps/api/js?sensor=false&language=$language");
	}else{
		if(JRequest::getVar('layout') == 'mobile'){
			$doc->addScript("http://maps.google.com/maps/api/js?sensor=true&language=$language");
		}else{
			$doc->addScript("http://maps.google.com/maps/api/js?sensor=false&language=$language");
		}
	}

	
	$gmap_width 		= $params->get( 'item_gmap_width','420');
	$gmap_height 		= $params->get( 'item_gmap_height','400');
	
	
	$doc->addStyleSheet( 'components/com_locator/assets/locator.css' );
	
	if($params->get('showmaponitempage',1) == 1){

		//hook the init function to the window's onload event.
		
		$view->initDOMLoadHook($params);
		$doc->addScriptDeclaration("jqLocator(document).ready(initialize);");

		
		
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
				contentString += '<?php echo str_replace("'","\'",$view->formatItem($item,$params,'map_address_template')); ?>';

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
			<?
		
		$buffer = ob_get_contents();
		ob_end_clean();
		$doc->addScriptDeclaration($buffer);
	}

	?>
<html>
<head>
<title></title>
<meta name="viewport" content="user-scalable=no, width=device-width" />
<link rel="stylesheet" type="text/css" href="components/com_locator/assets/locator_mobile.css" />
<?php
//media="only screen and (max-width: 480px)" 
foreach ($doc->_scripts as $script=>$type){

	echo '<script type="' . $type . '" src="' . $script . '"></script>
	';
}

foreach ($doc->_script as $type=>$script){

	echo '<script type="' . $type . '">'.$script.'</script>
	';
}
?>
</head>
<body>
		<div class="locator_text">
			<div class="locator_back"><a href="javascript:history.go(-1);"><?php echo JText::_('Back'); ?></a></div>
			
			<div class="address">
			<?php
				
			$format = $view->formatItem($item,$params,'address_template');
			
			if($params->get('triggercontentplugin',0) == 1){
				$format = JHTML::_('content.prepare', $format);
			}
			

			//output the item template 
			?>
			<div class="com_locator_address">
				<?php
					echo $format;	
				?>
			<a href="javascript:void(0);" onclick="showDirections(<?php echo 0; ?>,<?php echo (string)$item->lat; ?>,<?php echo (string)$item->lng; ?>);" ><?php echo JText::_('LOCATOR_GET_DIRECTIONS'); ?></a>
			<div id="0_directions" style="height:40px;"></div>		
			</div>	
		</div>
	</div>
	<div id="locator_map_directions" class="locator_combined_directions"></div>
	<div id="locator_map_canvas" class="locator_combined_gmap" style="margin:0px; padding:0px; width:100%; height:70%; <?php echo $hide_map; ?>"></div>

	
</body>
</html>