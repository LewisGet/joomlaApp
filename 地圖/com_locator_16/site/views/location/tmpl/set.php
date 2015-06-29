<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: default.php 534 2010-10-12 16:13:05Z fatica $
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

	$params = &JComponentHelper::getParams( 'com_locator' );
	
	$menuitemid = JRequest::getInt( 'Itemid' );
	  
	if ($menuitemid)
	{
		$menu = JSite::getMenu();
		$menuparams = $menu->getParams( $menuitemid );
		$params->merge( $menuparams );
	}
	
	$doc =& JFactory::getDocument();
	$doc->addStyleSheet( 'components/com_locator/assets/locator.css' );

				

	
	//default to the US view 
	$gmap_default_lat = $params->get( 'gmap_default_lat',41.397);
	$gmap_default_lng = $params->get( 'gmap_default_lng',-96.644 );
	$gmap_default_zoom = $params->get( 'gmap_default_zoom',1 );
		

	$linktoitempage = $params->get( 'linktoitempage',1 );

		$doc->addScript("http://maps.google.com/maps/api/js?sensor=false");
			
		$doc->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js");
		
		$doc->addScript("http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js");
		
		$doc->addStyleSheet( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );

		$defaultmapview = $params->get( 'defaultmapview','ROADMAP' );
		
		$gmap_width = $params->get( 'item_gmap_width','447');
		$gmap_height = $params->get( 'item_gmap_height','344');
		
		//hook the init function to the window's onload event.
		if($params->get('useondomready',1) == 1){
			$doc->addScriptDeclaration("window.onDomReady(modal_initialize);");
		}else{
			$doc->addScriptDeclaration("window.onload = modal_initialize;");
		}
		ob_start();
		
		?>
		
var map; 
var marker;
 var geocoder;

 
 
	jQuery(document).ready(function() {	

							     
						modal_initialize();
						
						var sliderZoom  = <?php echo $gmap_default_zoom; ?> * 10;
						 	   
						  	jQuery("#slider").slider({value:sliderZoom, change: function(event, ui){
						     	
						  			
						     		var zoom = (ui.value / 10)  + 1;
						     		map.setZoom(parseInt(zoom));
						     		jQuery("#zoom").val(zoom);
						     		
						     	}
						     }
						     );
						     

		  				 });
		  				 

  function modal_initialize(position) {
  
    geocoder = new google.maps.Geocoder();

	var latlng = new google.maps.LatLng(<?php echo $gmap_default_lat; ?>, <?php echo $gmap_default_lng; ?>);
	zoom =  10;
	sliderZoom  = <?php echo $gmap_default_zoom; ?> * 10;
	document.getElementById('zoom').value  = <?php echo $gmap_default_zoom; ?>;
	
	jQuery("#slider").slider( "option", "value", sliderZoom);

    var myOptions = {
      zoom: zoom,
      center: latlng,
        disableDefaultUI: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    map = new google.maps.Map(document.getElementById('modal_locator_map_canvas'), myOptions);
    
    google.maps.event.addListener(map, 'click', function(event) {
	    placeMarker(event.latLng);
	    document.getElementById('lat').value =event.latLng.lat()
     	document.getElementById('lng').value =event.latLng.lng()
     	//document.getElementById('location').value = event.latLng;
          	
    geocoder.geocode({'latLng': event.latLng}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[1]) {
         document.getElementById('address').value = results[1].formatted_address;
        }
      }
     	
  	});
     	
     	
  	});
   
  }  

  function placeMarker(location) {
  
  	  if(!marker){
  
		  marker = new google.maps.Marker({
		    position: location,
		    map: map
		  });
		  
	  }else{
	  		marker.setPosition(location);

	  }

  }  

  function updateMap(postalCode){

  
	var geocoder = new google.maps.Geocoder();
		
	geocoder.geocode( { 'address': postalCode.value,'region': 'US'},function(results,status){

		if (status != google.maps.GeocoderStatus.OK) {
			if(postalCode.length > 0){
			  alert('cannot geocode address provided'); 
			}
	    }else{
	     
     		map.setCenter(new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng()));
     		
     		document.getElementById('lat').value = results[0].geometry.location.lat();
     		document.getElementById('lng').value = results[0].geometry.location.lng();
     		
     		
     		jQuery("#slider").slider( "option", "value", 70 );
     		
     		placeMarker(results[0].geometry.location);

	    }
		
	});
	
					
	}	
			<?
		
		$buffer = ob_get_contents();
		ob_end_clean();
		$doc->addScriptDeclaration($buffer);

	//change this to loadItemTemplate
	?>
	
<div class="com_locator_set">
	<form name="setLocation">
	<div id="modal_locator_map_canvas" class="" style="width:<?php echo $gmap_width; ?>px; height:<?php echo $gmap_height; ?>px;"></div>	
	
	<div id="com_locator_controls">
		<div id="pn_step_one"></div>
		<input id="address" name="address" type="text" />
		<a id="pn_search" href="javascript:void(0);"  onclick="updateMap(document.getElementById('address'));" "><span>Search</span></a>
		
		<div id="pn_step_two"></div>
		
		<div id="pn_slider_bar">
		<div id="slider"></div>
		</div>
		
		<a id="pn_save" href="javascript:void(0);" onclick="saveLocation();"><span>Search</span></a>
		<a id="pn_exit" href="javascript:void(0);" onclick="window.parent.document.getElementById( 'sbox-window' ).close();"><span>Search</span></a>

		<input type="hidden" name="lat" id="lat" />
		<input type="hidden" name="lng" id="lng" />
		<input type="hidden" name="zoom" id="zoom" />
		
	</div>
	</form>	
</div><!-- end com_locator entry single -->	
<script language="javascript">

  function calculateBounds(map, center, zoom, width, height) {
    var p  = map.getProjection();
    var c  = p.fromLatLngToPoint(center);
    var z  = 1 << zoom;
    var dx = (0.5*width)/z;
    var dy = (0.5*height)/z;

    return new google.maps.LatLngBounds(
      p.fromPointToLatLng(
        new google.maps.Point(c.x-dx, c.y+dy)
      ),
      p.fromPointToLatLng(
        new google.maps.Point(c.x+dx, c.y-dy)
      )
    );
  }

function saveLocation(){
	
	var lat = jQuery("#lat").val();
	var lng = jQuery("#lng").val();
	var zoom = jQuery("#zoom").val();
	
	//alert(map.getCenter());
	bounds = calculateBounds(map, map.getCenter(), zoom, <?php echo $gmap_width; ?>, <?php echo $gmap_height; ?>);
	
	var range =  (Math.abs(bounds.getSouthWest().lng()) -  Math.abs(bounds.getNorthEast().lng())) ;
	
	//alert(range);
	
	var url = 'index.php?option=com_locator&task=saveuserlocation&tmpl=component&lat=' + lat + '&lng=' + lng + '&zoom=' + parseInt(zoom);

	jQuery.get(url, function(data) {
  			
  		alert("Neighborhood saved");
  	
  		
  		window.parent.document.getElementById( 'sbox-window' ).close();
  		
  		window.parent.location.reload();
  	
	});
	
	}


</script>