<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$config =& JFactory::getConfig();

$params = &JComponentHelper::getParams( 'com_locator' );

$mainframe = JFactory::getApplication();

if(count($this->lists['geocode']) > 0){
	$mainframe->enqueueMessage("Multiple Locations selected");
}

JHTML::_('behavior.tooltip');

$item = $this->items[0];

$params = &JComponentHelper::getParams( 'com_locator' );

$gmap_base_tld = $params->get( 'gmap_base_tld','US');
	
$doc =& JFactory::getDocument();
$doc->addScript("http://maps.google.com/maps/api/js?sensor=false");
$doc->addScript(JURI::root() . 'components/com_locator/assets/jquery.min.js' );

$doc->addScriptDeclaration('var jqLocator = {};
jqLocator = jQuery.noConflict();');
	

?>
<script language="javascript" type="text/javascript">

submitbutton = function (pressbutton) {
	
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}

	form.submit();
}

if(typeof(Joomla) != 'undefined'){
	Joomla.submitbutton	= submitbutton;
}

var location_id;

function unitGeocode(address,id,width){

	var geocoder = new google.maps.Geocoder();

	geocoder.geocode( { 'address': address,'region': document.getElementById('lng' + id).value},function(results,status){

	if (status == google.maps.GeocoderStatus.OK) {
			
		var url="index.php?option=com_locator&format=raw&task=savegeocode&lng="+results[0].geometry.location.lng()+"&lat="+results[0].geometry.location.lat()+"&id=" +id + "&tld=" + document.getElementById('lng' + id).value;

		jqLocator.ajax({
			url: url,
			success: function(data){
		
		  		document.getElementById('lat' + id).value = results[0].geometry.location.lat();
		  		document.getElementById('lng' + id).value = results[0].geometry.location.lng();
	
		  		document.getElementById('internal').style.width = width;
	
			  		if(width == "100%"){
						alert("Geocoding Complete. All data is saved");
			  		}
			}
		});
		
		/*
		Deprecated MooTools integration
		var a=new Ajax(url,{
			method:"get",
			parameters: {},
			onComplete: function(){
	
		  		document.getElementById('lat' + id).value = results[0].geometry.location.lat();
		  		document.getElementById('lng' + id).value = results[0].geometry.location.lng();
	
		  		document.getElementById('internal').style.width = width;
	
			  		if(width == "100%"){
						alert("Geocoding Complete. All data is saved");
			  		}
				}
	
				}).request();
				*/
		}else{
			
			switch(status){
				case google.maps.GeocoderStatus.ZERO_RESULTS:{
					document.getElementById('lat' + id).value = "No Results";
					
				}break;
				case google.maps.GeocoderStatus.OVER_QUERY_LIMIT:{
					document.getElementById('lat' + id).value = "Over Query Limit";
					alert("Geocoding cannot continue.  MetaLocator has limited the number of query Geocoding results from this IP address to 2500 per 24 hour period.  You may continue in 24 hours or from a different computer. ");
					width = "100%";		
				}break;
				case google.maps.GeocoderStatus.INVALID_REQUEST:{
					document.getElementById('lat' + id).value = "Invalid Request";
				}break;								
			}

			document.getElementById('lng' + id).value = "";
			document.getElementById('internal').style.width = width;

	  		if(width == "100%"){
				alert("Geocoding Complete. All data is saved");
	  		}			

			
		}
		
	});
}

function geocode(){

	var address;
	var width;

	<?php
	$i = 0; 
	
	if(count($this->lists['geocode']) > 0){
		$total = count($this->lists['geocode']);	
	
		
		foreach ($this->lists['geocode'] as $id=>$address){
			
			$i++;
			
		?>
		  setTimeout('unitGeocode(document.getElementById(\'address<?php echo $id;?>\').value,\'<?php echo $id;?>\',parseInt(\'<?php echo ($i / $total) * 100;?>\') + \'%\')',<?php echo $i * 1000 ?>);
	<?php 
		} 
	}
 ?>
}

function clearcache(){
			
var url="index.php?option=com_locator&task=clearcache";

jqLocator.ajax({
			url: url,
			success: function(data){
				alert("All postal code search cache has been removed. ");
			}
		});
}
//-->

</script>

<form action="index.php" method="post" name="adminForm">
<fieldset>

<div id="remaining">You have <span class="hasTip" title="Resets every 24 hours or change of IP address"><?php echo $this->remaining; ?></span> on-demand geocoding requests remaining today.</div>
<?php 
$i = 0;

if(count($this->lists['geocode'])){
	
?>
<div id="progress" style="width:100%;height:25px;border:1px solid #C3D2E5; border-bottom:3px solid #84A7DB;border-top:3px solid #84A7DB;">
<div id="internal" style="height:25px;background-color:#C3D2E5;width:0px;"></div>
</div>

<?php
$mainframe = JFactory::getApplication();
if($mainframe->isAdmin()){ ?>
<table>
<tr>
<td>
<a href="javascript:geocode();" ><img src="components/com_locator/images/go_f2.png" border="0" alt="Geocode!"/><br />Geocode!</a></td>
<td>
<a href="javascript:clearcache();" ><img src="components/com_locator/images/cancel_f2.png" border="0" alt="Clear Geocode Cache!  This clears geocoded postal codes obtained while users are using the postal code search feature."/><br />Clear geocode cache</a>
</td>
</tr>
</table>
<?php } ?>		
<table>
<tr><th>ID</th><th>Address (As Geocoded)</th><th>TLD</th><th>Lat</th><th>Lng</th></tr>
<?php
foreach ($this->lists['geocode'] as $id=>$address){

	?>
	<tr>
	<td><input type="text" name="location_id[]" value="<?php echo $id; ?>" size="10" /></td>
	<td><input type="hidden" id="address<?php echo $id; ?>" name="address[]" value="<?php echo $address;?>" /><a title="Click to edit this location" href="index.php?option=com_locator&task=edit&id=<?php echo $id; ?>"><?php echo str_replace("+"," ",$address); ?></a></td>
	
	<td><input type="text" id="tld<?php echo $id; ?>" name="tld[]" value="<?php echo $this->lists['tld'][$id]; ?>" size="3"/></td>
	
	<td><input type="text" id="lat<?php echo $id; ?>" name="lat[]" value="" /></td>
	<td><input type="text" id="lng<?php echo $id; ?>" name="lng[]" value="" /> </td>
	</tr>
	<?php 
	$i++;
}

?>
</table>
</fieldset>
<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid',''); ?>"/>
<input type="hidden" name="option" value="com_locator" />
<?php $ids = JRequest::getVar('cid'); 
foreach ($this->lists['geocode'] as $id=>$address){
?>
<input type="hidden" name="cid[]" value="<?php echo $id ?>" />
<?php 
}
?>

<?php
}else{//end if have locations
?>
<h2>No locations found</h2>
<?php
}
?>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="task" value="savemultiple" />
</form>
<?php echo JHTML::_('behavior.keepalive'); ?>