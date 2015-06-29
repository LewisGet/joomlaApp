<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$item =&$this->item;

$format = $this->params->get('address_template');
$target = '';	
$i = $this->index;

$menuitemid = JRequest::getInt( 'Itemid' );
$linktoitempage = $this->params->get( 'linktoitempage',1 );

?>
	myLatlng = new google.maps.LatLng(<?php echo (string)$item->lat; ?>,<?php echo (string)$item->lng; ?>);
	
	contentString = '<div class="popupWindow">';
	<?php 
	$map_content = $this->formatItem($item,$this->params,'map_address_template');
	$map_content = str_replace("'","\'",$map_content);
	?>
	
	contentString += '<?php echo $map_content; ?>';
	<?php if ( $this->params->get( 'show_directions', 1 ) == 1){ ?>
	//add the driving directions code
	contentString += '<a href="javascript:void(0);" onclick="showDirections(<?php echo (int)$i; ?>,<?php echo (string)$item->lat; ?>,<?php echo (string)$item->lng; ?>);" ><?php echo JText::_('LOCATOR_GET_DIRECTIONS'); ?></a><div id="<?php echo (int)$i; ?>_directions" style="height:40px;"></div><br />';
	<?php } ?>			
	contentString += '</div>';
	
    infoWindows[<?php echo $i; ?>] = new google.maps.InfoWindow({
        content: contentString
    });
    
	latlng[<?php echo $i?>] = myLatlng;
	<?php 
		$icon = "";
		$shadow = "";
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
	?>
		 	
	 markers[<?php echo $i; ?>] = new google.maps.Marker({
		'position': myLatlng,
		map: map,
		<?php echo $icon; ?>
		<?php echo $shadow; ?>
		title:'<?php echo addslashes(html_entity_decode($item->title,ENT_QUOTES, "UTF-8")); ?>'

	});
	
	
	google.maps.event.addListener(markers[<?php echo $i; ?>], 'click', function() {
	 
	 	//close any open window
	 	if(openWindow >= 0){
	 		infoWindows[openWindow].close();
	 	}
	 		 		
  		<?php if ( $this->params->get( 'google_analytics', 0 ) == 1){ ?>
  		if(typeof(_gaq) != 'undefined'){
	  		_gaq.push(['_trackEvent', 'Locations', 'OpenMarkerWindow', markers[<?php echo $i; ?>].title]);
  		}
	  	<?php } ?>
	
      	infoWindows[<?php echo $i; ?>].open(map,markers[<?php echo $i; ?>]);
      	
      	openWindow = <?php echo $i; ?>;
    });