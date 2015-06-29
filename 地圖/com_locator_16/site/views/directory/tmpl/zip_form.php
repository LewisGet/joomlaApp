<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: zip_form.php 959 2011-11-22 11:44:30Z fatica $
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

@define('LOCATOR_POSTAL_CODE', 1);

jimport( 'joomla.html.parameter' );

$component = JComponentHelper::getComponent( 'com_locator' );
$params = new JParameter( $component->params );
$menuitemid = JRequest::getInt( 'Itemid' );
  
if ($menuitemid)
{
	$menu = JSite::getMenu();
	$menuparams = $menu->getParams( $menuitemid );
	$params->merge( $menuparams );
}

//are we in the module?
if(defined('LOCATOR_MODULE')){
	
	//tell the submit button that we're there.
	@define('MODULE_LOCATOR_POSTAL_CODE', 1);
	
	if(isset($module_params)){
		$params->merge($module_params);	
	}	

}

$requirepostalcode = $params->get('requirepostalcode');

$gmap_api_key = $params->get( 'gmap_api_key' );
$gmap_base_tld = $params->get( 'gmap_base_tld','US');

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

$distance_unit_label = JText::_($params->get( 'distance_unit','LOCATOR_M'));

$postal_code_label = JText::_($params->get( 'postal_code_label','LOCATOR_POSTAL_CODE'));

$distances = JText::_($params->get( 'distances','5,10,25,50,100'));

$distance_array = explode(",",$distances);

if(count($distance_array) == 0){
	$distance_array = explode(",",'5,10,25,50,100');
}

?>
<script language="javascript" type="text/javascript">
<!-- 

var countryElement = 'country';
	
function <?php if(defined('LOCATOR_MODULE')){ echo "module_"; }?>sendPostalQuery(postalCode,f){
	
	<?php 
	//if we show the country drop-down, you must pick a country, since we need to bias the geoocoder
	if($params->get('showcountrydropdown') == 1){
	?>
	
	//are we checking the module's country choice (in case they're displayed on the same page
	if(f.name == "locatorModule"){
		countryElement = 'module_country';
	}

	//if country is displayed and postal code is provided, a country must be selected
	if(document.getElementById(countryElement)){
		if(postalCode.length != 0){
			if(document.getElementById(countryElement).options[document.getElementById(countryElement).options.selectedIndex].value == ""){
				alert("<?php echo JText::_('LOCATOR_ERROR_COUNTRY'); ?>");	
				return false;
			}
		}
	}
	<?php
	}
	?>
	
  	<?php if ( $params->get( 'google_analytics', 0 ) == 1){ ?>
  	if(typeof(_gaq) != 'undefined'){
  		_gaq.push(['_trackEvent', 'Locations', 'Search',postalCode]);
  	}
  	<?php } ?>

	if(postalCode.length == 0){
 
		<?php 
		if ($requirepostalcode == 1){
		?>
		alert("<?php echo JText::_('LOCATOR_ERROR_POSTALCODE'); ?>");
		<?php
		}else{
		?>
		f.submit();
		<?php } ?>		
		
	}else{
			
		var Itemid = -1;
		if(document.getElementById('Itemid')){
			Itemid = document.getElementById('Itemid').value;	
		}
		var framed = 0;
			    	
		if(document.getElementById('framed')){
			framed = document.getElementById('framed').value;	
		}
		//check if we have the search location
		var url = "<?php echo JRoute::_('index.php?option=com_locator&view=directory&format=raw&task=checkpostalcode&no_html=1',false); ?>&postal_code=" + escape(postalCode) + '&Itemid=' + Itemid + '&framed=' + framed;

		jqLocator.ajax({
			url: url,
			success: function(data){successPostalSearch(data,f,postalCode)}
		});
		
	}
}


function successPostalSearch(responseText,f,postalCode){

	//we have the postal code, just search -- this could return 1 if the response just happened to contain a 1
	var resp = responseText.replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
	
	if(resp == '1'){
		
		f.submit();
		
	}else if(resp == '0'){
		
		try {	
								
			//geocode the postal code, save for future use
			var geocoder = new google.maps.Geocoder();
		
			var searchPhrase;
			
			searchPhrase = postalCode;
									
			//are we checking the module's country choice (in case they're displayed on the same page
			if(f.name == "locatorModule"){
				countryElement = 'module_country';
			}
	
			<?php 
			//if we show the country drop-down, you must pick a country, since we need to bias the geoocoder
			if($params->get('showcountrydropdown') == 1){
			?>
				searchPhrase += ' ' + document.getElementById(countryElement).value;
			<?php
			}
			?>
			
			geocoder.geocode( { 'address': searchPhrase,'region': '<?php echo strtolower($gmap_base_tld); ?>'},function(results,status){
				
				
				if (status != google.maps.GeocoderStatus.OK) {
					
			        alert("<?php echo JText::_('LOCATOR_ERROR_POSTALCODE'); ?>");
			        
			    }else{
			    	var Itemid = -1;
			    	
					if(document.getElementById('Itemid')){
						Itemid = document.getElementById('Itemid').value;	
					}
					
					var framed = 0;
			    	
					if(document.getElementById('framed')){
						framed = document.getElementById('framed').value;	
					}
			    	
					var url2="<?php echo JRoute::_('index.php?option=com_locator&task=savegeocode',false); ?>&lng="+results[0].geometry.location.lng()+"&lat="+results[0].geometry.location.lat()+"&postal_code=" +escape(postalCode) + "&tld=" + "<?php echo strtolower($gmap_base_tld); ?>&Itemid=" + Itemid + "&framed=" + framed;
					jqLocator.ajax({
						url: url2,
						success: function(){
										//we got a lat/lng and saved it
										f.submit();
									},
						error:function(){
										 alert("<?php echo JText::_('LOCATOR_ERROR_POSTALCODE'); ?>");
									}
					});
	
	
		
					
			    }
				
			}); //end geocode
		
		}catch(e){
			
			alert("Error creating Google Map Geocoder object." + e);
			
		}
	
	//end else 0
	}else{
		alert("Error. Invalid response from postal code geocode check.  The following URL must return either a 1 or a 0:\n" + url);
	}
}	


function failPostalSearch(){
	
}

var nonChar = false;

function handleKeypress(e) {

    var char;
    var evt = (e) ? e : window.event;       //IE reports window.event not arg
    if (evt.type == "keydown") {
        char = evt.keyCode || evt.which;

        if (char == 13) {                   	// Delete Key (Add to these if you need)
        	return handleChar(char);            // function to handle non Characters
        } 
    }

  return true;
}


function handleChar(char){
	
	if(char == 13){
			<?php if(defined('LOCATOR_MODULE')){ echo "module_"; }?>sendPostalQuery(document.getElementById('<?php if(defined('LOCATOR_MODULE')){ echo "module_"; }?>postal_code').value,<?php if(defined('LOCATOR_MODULE')){ echo "document.locatorModule"; }else{ echo "document.adminForm"; }?>);
	}
	
	return false;

}
-->
</script>
<div class="locator_form postal">
	<div class="inner">
	<span class="label"><?php echo $postal_code_label; ?></span><input type="text" name="postal_code" id="<?php if(defined('LOCATOR_MODULE')){ echo "module_"; }?>postal_code" class="inputbox" value="<?php echo JRequest::getString('postal_code');?>" />
	<?php if ($requirepostalcode == 1){ ?>
	<span class="required">*</span>
	<?php } ?>
	
	<?php echo JText::_('LOCATOR_SHOW_WITHIN'); ?>
		<select name="radius" id="<?php if(defined('LOCATOR_MODULE')){ echo "module_"; }?>radius" class="inputbox" >
			<?php 
				
				foreach($distance_array as $dist){
					?>
						<option value="<?php echo (float)$dist;?>" <?php echo (JRequest::getString('radius') == $dist)?("selected='selected'"):(""); ?> ><?php echo $dist;?> <?php echo $distance_unit_label; ?></option>		
					<?php 
				}
			
			?>
		</select>
	</div>
</div>
<script language="javascript" type="text/javascript">
	document.getElementById('<?php if(defined('LOCATOR_MODULE')){ echo "module_"; }?>postal_code').onkeydown = handleKeypress;
	document.getElementById('<?php if(defined('LOCATOR_MODULE')){ echo "module_"; }?>radius').onkeydown = handleKeypress;
</script>