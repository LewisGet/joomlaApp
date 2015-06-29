<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$doc = JFactory::getDocument();

//if($params->get('autofind',0) == 1){
//	$doc->addScript("http://code.google.com/apis/gears/gears_init.js");	
//}

function showInitiallyOpen(&$params){
	
	if ( $params->get( 'initially_open', 0 ) > 0){
		$open = (int)$params->get( 'initially_open', 0 );
		?>
		openWindow 
		
		<?php
	}
	
}

function showMarkerClusterer(&$params){
	
	$doc = JFactory::getDocument();
	
	if ( $params->get( 'clusterer', 0 ) == 1){
		$doc->addScript("components/com_locator/assets/markerclusterer.js");
	?>
		
	var mcOptions = {gridSize: <?php echo $params->get( 'cluster_gridsize',60 ); ?>, maxZoom: <?php echo $params->get( 'cluster_zoom',15 ); ?>};

	var markerCluster = new MarkerClusterer(map, markers, mcOptions);
	
	<?php
		
	}
	
}

function showCenterOnResults(&$params){
	
if ( $params->get( 'centeronresults', 1 ) == 1){ ?>
 
 	var latlngbounds = new google.maps.LatLngBounds();
 
	if(latlng.length > 0){
	
	  if(latlng.length == 1){
		   	
		   	latlngbounds.extend( latlng[ 0 ] );
		  	map.setCenter( latlngbounds.getCenter( ));
		  	map.setZoom(<?php echo $params->get( 'zoomlevelonsingle', 16 ); ?>);
		  
	  	}else{
	  
			for ( var i = 0; i < latlng.length; i++ )
			{
				
				if(typeof(latlng[ i ]) != 'undefined'){
					latlngbounds.extend( latlng[ i ] );
				}
			}
		  
		  map.setCenter(latlngbounds.getCenter());
		  map.fitBounds(latlngbounds);
		 
		  
	   }
	   	    
		 
	}

<?php }
}

?>

	var map;
	var openWindow = -1;
	var userLocation;
	var contentString;
	var latlng = new Array();
	var markers = new Array();
	var infoWindows = new Array();
	var directionsDisplay;
	var directionsService;
		
	
	function getTweet(username,index,id){
		
		if(username.length > 0){
		
			jqLocator.getJSON("http://twitter.com/status/user_timeline/"+username+".json?count=1&callback=?",
	
				function(data){
		
					if(data[0].geo){
						var lat = data[0].geo.coordinates[0];
						var lng = data[0].geo.coordinates[1];
						var date = data[0].created_at;
						var text = data[0].text;
						var user_profile_img = data[0].user.profile_image_url_https;
						var newLatLng;
						
						newLatLng = new google.maps.LatLng(lat,lng);
						
						if(newLatLng){
							markers[index].setPosition(newLatLng);
						
						//update the record with last update and the new lat/lng
						
							var url="<?php echo JRoute::_('index.php?option=com_locator&task=savegeocode',false); ?>&id="+id+"&lng="+lng+"&lat="+lat+"&postal_code=&tld=" + "<?php echo strtolower($params->get( 'gmap_base_tld','US')); ?>";
							jqLocator.ajax({
								url: url,
								success: function(){
											},
								error:function(){
												 alert("<?php echo JText::_('LOCATOR_ERROR_POSTALCODE'); ?>");
											}
							});
						}
					}
					
				//jqLocator("#twitter").html(lat + lng);
			});
		}
	}
	
	
	
	<?php
	//show the map overlays
	if($params->get('show_overlays',0) == 1){
	?>
	
	function showOverlays(){
	
		var path 	= new Array(<?php echo count($this->lists['polygons'])?>);
		var polygon = new Array(<?php echo count($this->lists['polygons'])?>);
		
		<?php
		foreach($this->lists['polygons'] as $tld=>$country){
		
			$x = 0;
			foreach($country as $polygon){
				?>
				path[<?php echo $x; ?>] = new Array(<?php echo count($polygon)?>);
				<?php
				$i = 0;
				foreach($polygon as $point){
					
					if(abs($point['lat']) > 0 && abs($point['lng'])){
						?>
						path[<?php echo $x; ?>][<?php echo $i; ?>] = new google.maps.LatLng(<?php echo $point['lat']; ?>,<?php echo $point['lng']; ?>);
						<?php
						$i++;
					}
					
				}
				?>
			  //href should be a search that drills to country search
			  polygon[<?php echo $x; ?>]  = new google.maps.Polygon({
				    paths: path[<?php echo $x; ?>],
				    strokeColor: "#FF0000",
				    strokeOpacity: 0.8,
				    strokeWeight: 1,
				    fillColor: "#FF0000",
				    fillOpacity: 0.35
				  });
				  
			  google.maps.event.addListener(polygon[<?php echo $x; ?>], 'click',function (){
			  	alert('<?php echo $tld; ?>');
			  });
	
			  polygon[<?php echo $x; ?>].setMap(map);	
				<?php
			$x++;	
			}
		}
		?>
	}
	
	
	
	<?php	
	}
	?>
	
	function getURLParameter( name )
	{
	  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	  var regexS = "[\\?&]"+name+"=([^&#]*)";
	  var regex = new RegExp( regexS );
	  var results = regex.exec( window.location.href );
	  if( results == null )
	    return "";
	  else
	    return results[1];
	}

	  function openMarkerWindow(i){
	  
	  	<?php if ( $params->get( 'google_analytics', 0 ) == 1){ ?>
	  	if(typeof(_gaq) != 'undefined'){
	  		_gaq.push(['_trackEvent', 'Locations', 'OpenMarkerWindow', markers[i].title]);
	  	}
	  	<?php } ?>
	  	
    	//close any open window
	 	if(openWindow >= 0){
	 		infoWindows[openWindow].close();
	 	}

	 	if(i < infoWindows.length){ 	
		 	infoWindows[i].open(map,markers[i]);
		    		 	
		 	openWindow = i;
	 	}else{
	 		alert("Error: This location has not been geocoded.  Click the address to search for the location on Google Maps.");
	 	}
	  }
	  
	  function route(start,lat,lng){
			  
	  	
		  var start = document.getElementById(start).value;
		  var end = lat + ' ' + lng;
		  
		  var request = {
		    origin:start, 
		    destination:end,
		    travelMode: google.maps.TravelMode.DRIVING
		    <?php if ($params->get( 'gmap_directions', '' ) == 'imperial'){ ?>
		     ,unitSystem: google.maps.UnitSystem.IMPERIAL
		    <?php } ?>
		     <?php if ($params->get( 'gmap_directions', '' ) == 'metric'){ ?>
		     ,unitSystem: google.maps.UnitSystem.METRIC
		    <?php } ?>
		  };

		  directionsService.route(request, function(result, status) {
		    if (status == google.maps.DirectionsStatus.OK) {
		      directionsDisplay.setDirections(result);
		    }
		  });
	  
	  }
	  
	  function showDirections(i,lat,lng){
	  	  
	  	var container = document.getElementById(i + '_directions');
	  	
	  	<?php if ( $params->get( 'google_analytics', 0 ) == 1){ ?>
  		if(typeof(_gaq) != 'undefined'){
	  		_gaq.push(['_trackEvent', 'Locations', 'getDirections', markers[i].title]);
  		}
	  	<?php } ?>
	  	
	  	if(document.getElementById(i + '_directions_start') == null){
	  	
		  	var label = document.createTextNode("<?php echo JText::_('LOCATOR_DIRECTIONS_PROMPT'); ?>"); 
		  	
		  	var fromAddress = document.createElement('input'); 
		  	
		  	fromAddress.setAttribute('type','text');
		  	fromAddress.setAttribute('id',i + '_directions_start');
		  	fromAddress.setAttribute('class','inputbox');
		  	
		  	<?php 
		  	//if were on the map item page and the config is set to autofind the user
		  	//prepopulate the from address with the current lat/lng
		  	if($params->get('autofind',0) == 1 && JRequest::getVar('view') == 'location'){ ?>
		  	
		  		if(document.getElementById('user_lat')){
		  			if(document.getElementById('user_lat').value != ''){
		  				fromAddress.setAttribute('value',document.getElementById('user_lat').value + ',' + document.getElementById('user_lng').value);
		  			}
		  		}
	
		  	<?php } ?>
	
		  	try {
            	var goButton = document.createElement('<input value="<?php echo JText::_('LOCATOR_DIRECTIONS_GO'); ?>" onclick="route(\''+i+'_directions_start\','+lat+','+lng+')" type="button">'); 
            } catch (e) {
		  	
            	var goButton = document.createElement('input');
		  		goButton.setAttribute('type','button');
		  		goButton.setAttribute('onclick','route(\''+i+'_directions_start\','+lat+','+lng+')');
            
		  		goButton.setAttribute('value','<?php echo JText::_('LOCATOR_DIRECTIONS_GO'); ?>');
            }
		  			  	
		  	container.appendChild(label);
		  	container.appendChild(fromAddress);
		  	container.appendChild(goButton);
		 
	  	}

	  }

	  function notAllowed(){
		document.getElementById('device_location').checked=false;
		document.getElementById('device_location').disabled=true;
	  }
	  
      function userPositionCallback(position){
      
      	//once the user location is found, submit the form
      	if(document.getElementById('user_lat')){
	      	document.getElementById('user_lat').value = position.coords.latitude;
	      	document.getElementById('user_lng').value = position.coords.longitude;
      	}
      	
      	if(document.getElementById('device_location')){
      		document.getElementById('device_location').disabled=false;
	      	document.getElementById('device_location').checked=true;
      	}
      	
      	var myLatlng = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
	     
      	//center around the user if we're not focused on a result set or a location
      	if(getURLParameter('task') != 'search_zip' && getURLParameter('view') == 'directory'){
      	
      	    //map.setCenter(myLatlng);
	    	//map.setZoom(10);
      			
	      	//uncomment this to perform the search without letting the user hit submit
	      	if(document.getElementById('user_lat')){
	      		if(Math.abs(parseFloat(document.getElementById('user_lat').value)) > 0){
	      			//alert(getURLParameter('task'));
	      			//document.adminForm.submit();
	      		}
      		}
      	}
      	
      }
      
		function initialize() 
		{
		
			try{
			
				var myLatlng;
				
				var myOptions = {
					zoom:<?php echo $gmap_default_zoom; ?>,
					center: new google.maps.LatLng(<?php echo $gmap_default_lat; ?>,<?php echo $gmap_default_lng; ?>),
					streetViewControl: true,
					mapTypeId: google.maps.MapTypeId.<?php echo $defaultmapview; ?>
				};
				
				if(document.getElementById("locator_map_canvas")){
					map = new google.maps.Map(document.getElementById("locator_map_canvas"), myOptions);
				}else{
					return;
				}
				
			}catch(e){
				alert("Error, cannot create Google Map object." + e);
				return;
			}	
			
			<?php if ( $params->get( 'show_directions', 1 ) == 1){ ?>
			
			directionsService = new google.maps.DirectionsService();	
			directionsDisplay = new google.maps.DirectionsRenderer();
			directionsDisplay.setMap(map);
			directionsDisplay.setPanel(document.getElementById("locator_map_directions"));
			
			<?php } ?>

			
			
			<?php
			//show the map overlays
			if($params->get('show_overlays',0) == 1){
			?>
			showOverlays();
			<?php
			}
			?>