<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 * 
 * 
 * Consumer key
 *  MiQA6HcMHCDB41jOXaVA
 *  Consumer secret
 * khcxzQ6Yrqf7b6rJ15y8Sd1ENrCJ4LSxnVigVbOM0
 * Request token URL
 * http://twitter.com/oauth/request_token
 * Access token URL
 * http://twitter.com/oauth/access_token
 * Authorize URL
 * http://twitter.com/oauth/authorize *We support hmac-sha1 signatures. We do not support the plaintext signature method.
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

$app = JFactory::getApplication();

//check that the template was not loaded
if(JRequest::getString('tmpl','') != 'component'){

	$active = $menu->getActive();
	$url = $active->link . '&Itemid=' . $menuitemid . '&tmpl=component';
	
	$app->redirect($url);	
}

$this->initDOMLoadHook($params);

$this->initDOMLoadHook($params,'findUserLocation');

$doc->addScript("http://maps.google.com/maps/api/js?sensor=true");

$maxresults = 			(int)$params->get('maxresults',0);
$gmap_default_lat = 	$params->get( 'gmap_default_lat',41.397);
$gmap_default_lng = 	$params->get( 'gmap_default_lng',-96.644 );
$gmap_default_zoom = 	$params->get( 'gmap_default_zoom',4 );
$gmap_height = 			$params->get( 'gmap_height','250');
$distance_unit_label = 	JText::_($params->get( 'distance_unit','LOCATOR_M'));
$defaultmapview = 		$params->get( 'defaultmapview','ROADMAP' );
$showemptymap = 		$params->get( 'showemptymap','0');

$html_output = '';

ob_start();

?>		
function userPositionCheckinCallback(position){

	//once the user location is found, submit the form
	if(document.getElementById('lat')){
  		document.getElementById('lat').value = position.coords.latitude;
  		document.getElementById('lng').value = position.coords.longitude;
	}
	
	var myLatlng = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
	var infoWindow;
	map.setCenter(myLatlng);
	map.setZoom(10);
	
	//add a marker
	var marker = new google.maps.Marker({
			position: myLatlng, 
		 	map: map,
		 	title:'<?php echo addslashes(html_entity_decode('Your Location',ENT_QUOTES, "UTF-8")); ?>'
	});
	
	google.maps.event.addListener(marker, 'click', function() {
	
      	infoWindow.open(map,marker);
      	
    });

}

function findUserLocation(){
	// Try W3C Geolocation (Preferred)
	if(navigator.geolocation) {
		browserSupportFlag = true;
		navigator.geolocation.getCurrentPosition(userPositionCheckinCallback, notAllowed);
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
}

<?php

$path = JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'directory' . DS . 'mapinit.php';

require_once($path);

?>
}  //end init function
<?php	
$buffer = ob_get_contents();
ob_end_clean();
$doc->addScriptDeclaration($buffer);
?>
<!DOCTYPE html> 
<html> 
    <head> 
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
        <title>Hello World</title> 
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
        <script src="components/com_locator/assets/sencha/sencha-touch.js" type="text/javascript"></script> 
        <link href="components/com_locator/assets/sencha/resources/css/sencha-touch.css" rel="stylesheet" type="text/css" /> 
 		<link rel="stylesheet" type="text/css" href="components/com_locator/assets/locator_mobile.css" />
        <script type="text/javascript"> 
 
            new Ext.Application({
                launch: function() {
                    new Ext.Panel({
                        fullscreen: true,
                        html: 'Hello World!'
                    });
                }
            });
 
        </script> 
 
    </head> 
    <body>
    <h2>Your Location</h2>
    
	<form action="index.php" method="post" name="adminForm">
	 
	<div id="locator_map_canvas" class="locator_combined_gmap" style="width:100%; height:30%;";>
	
	</div>
	
	<fieldset style="width:100%; float:left;">
	<legend>Check In Details</legend>
	<?php 
	if(isset($this->lists['tags'])){ ?>
	
		<?php echo JText::_('LOCATOR_TAGS'); ?>:
		
		<?php echo $this->lists['tags']; ?>
	<?php 
	} 
	?>
	<br />Restaurant Name: <input id="name" name="name" /><span class="required">*</span>
	
	</fieldset>
	<input id="lng" name="lng" type="hidden" />
	<input id="lat" name="lat" type="hidden" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="view" value="location" />
	<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid',''); ?>"/>
	<input type="hidden" name="option" value="com_locator" />
	<input type="hidden" name="id" value="<?php echo JRequest::getInt('id'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="task" value="save" />
	<input type="submit" name="submit" value="Submit" />
	</form>
	<?php echo JHTML::_('behavior.keepalive'); ?>
    </body> 
</html>
