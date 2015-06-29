<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$doc =& JFactory::getDocument();
$params = &JComponentHelper::getParams( 'com_locator' );
$menuitemid = JRequest::getInt( 'Itemid' );

if ($menuitemid)
{
	$menu = JSite::getMenu();
	$menuparams = $menu->getParams( $menuitemid );
	$params->merge( $menuparams );
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

if($params->get('use_ssl',0) == 1){
	$doc->addScript("https://maps-api-ssl.google.com/maps/api/js?sensor=false");
}else{
	$doc->addScript("http://maps.google.com/maps/api/js?sensor=false");
}

if($params->get('autofind',0) == 1){
	$doc->addScript("http://code.google.com/apis/gears/gears_init.js");	
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
			latlngbounds.extend( latlng[ i ] );
		}
	  
	  map.setCenter(latlngbounds.getCenter());
	  map.fitBounds(latlngbounds);
	  
   }
}


<?php }	
}

?>
var map;
var openWindow;
var userLocation;
var latlng = new Array();
var markers = new Array();
var infoWindows = new Array();
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();	

function openMarkerWindow(i){

//close any open window
	if(openWindow > 0){
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
    travelMode: google.maps.DirectionsTravelMode.DRIVING
  };
  
  directionsService.route(request, function(result, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(result);
    }
  });

}

function showDirections(i,lat,lng){
	  
	var container = document.getElementById(i + '_directions');
	
	if(document.getElementById(i + '_directions_start') == null){
	
  	var label = document.createTextNode("<?php echo JText::_('LOCATOR_DIRECTIONS_PROMPT'); ?>"); 
  	
  	var fromAddress = document.createElement('input'); 
  	
  	fromAddress.setAttribute('type','text');
  	fromAddress.setAttribute('id',i + '_directions_start');
  	fromAddress.setAttribute('class','inputbox');

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


function userPositionCallback(position){

	//once the user location is found, submit the form
	document.getElementById('user_lat').value = position.coords.latitude;
	document.getElementById('user_lng').value = position.coords.longitude;
	<?php if(!strlen(JRequest::getFloat('user_lat'))){ ?>
	document.adminForm.submit();
	<?php } ?>
	
}	

function initialize() 
{
	try{
		var myLatlng = new google.maps.LatLng(<?php echo $gmap_default_lat; ?>,<?php echo $gmap_default_lng; ?>);
		var myOptions = {
			zoom:<?php echo $gmap_default_zoom; ?>,
			center: myLatlng,
			streetViewControl: true,
			mapTypeId: google.maps.MapTypeId.<?php echo $defaultmapview; ?>
		}
		
	}catch(e){
		alert("Error, cannot create Google Map object.");
		return;
	}
		
	 
	map = new google.maps.Map(document.getElementById("locator_map_canvas"), myOptions);
	
	directionsDisplay = new google.maps.DirectionsRenderer();
	directionsDisplay.setMap(map);
	directionsDisplay.setPanel(document.getElementById("locator_map_directions"));
	
	<?php if($params->get('autofind',0) == 1){ ?>
	
	// Try W3C Geolocation (Preferred)
	if(navigator.geolocation) {
		browserSupportFlag = true;
		navigator.geolocation.getCurrentPosition(userPositionCallback, function() {});
	// Try Google Gears Geolocation
	} else if (google.gears) {
		browserSupportFlag = true;
		var geo = google.gears.factory.create('beta.geolocation');
		geo.getCurrentPosition(userPositionCallback, function() {});
	// Browser doesn't support Geolocation
	} else {
		browserSupportFlag = false;
		userLocation = false;
	}
<?php } 

if(isset($this->items)){
	
	for ($i = 0; $i < count($this->items);  $i++){
		
		$item = $this->items[$i];
	
		$j = $i + 1;
		
		for ($i = 0; $i < count($this->items);  $i++){
			
			$item = $this->items[$i];
	
			$this->item =& $item;
			$this->params =&$params;
			$this->index = $i;
			
			if($item->id > 0 && strlen($item->lat) > 0){		
					
				$this->setLayout('combined');
				echo $this->loadTemplate('map_item');
				
			}
		}
	}
	
	showCenterOnResults($params);
}

?>
}//end initialize