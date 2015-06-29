<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: form.php 952 2011-11-13 11:07:04Z fatica $
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$config =& JFactory::getConfig();

$params = &JComponentHelper::getParams( 'com_locator' );

$menuitemid = JRequest::getInt( 'Itemid' );
  
if ($menuitemid)
{
	$menu = JSite::getMenu();
	$menuparams = $menu->getParams( $menuitemid );
	$params->merge( $menuparams );
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

$allownogeo = $params->get( 'allownogeo',0 );
$gmap_base_tld = $params->get( 'gmap_base_tld','US');

$mainframe = JFactory::getApplication();
 
$doc =& JFactory::getDocument();

$language = $params->get('gmap_language','en-GB');

if($params->get('use_ssl',0) == 1){
	$doc->addScript("https://maps-api-ssl.google.com/maps/api/js?sensor=false&language=$language");
}else{
	$doc->addScript("http://maps.google.com/maps/api/js?sensor=false&language=$language");
}

define('_GINIT',1);

$item = $this->items[0];

$lang = JFactory::getLanguage();

$lang->load('com_locator');

?>
<script language="javascript" type="text/javascript">

function showAddress(address) {

	var form = document.adminForm;
	
	try{
		
		var geocoder = new google.maps.Geocoder();
	
		geocoder.geocode( { 'address': address,'region': '<?php echo $gmap_base_tld; ?>'},function(results,status){
	
			if (status == google.maps.GeocoderStatus.OK) {
		
			  		document.getElementById('lat').value = results[0].geometry.location.lat();
			  		document.getElementById('lng').value = results[0].geometry.location.lng();
					form.submit();
			}else{
	        	
				alert('<?php echo JText::_('LOCATOR_NOGEO'); ?>');
		       
		        <?php
				//submit the location if we're allowing 
		        if($allownogeo == 1){ 
		        ?>
		        	form.submit();
		        <?php } ?>				
			}
			
		});
		
	}catch(e) {
		 form.submit();
	}

}

function validate(){
	
	//validate the entry
	if(document.getElementById('name').value == ''){
		alert('<?php echo JText::_('LOCATOR_NAME_INVALID'); ?>'); 
		return false;
	}

	return true;
	
}

function submitbutton(pressbutton) {
	
	var form = document.adminForm;
	
	try {
		form.onsubmit();
	} catch(e) {

	}

		//geocode the address provided
		var address = document.getElementById('Address').value + '+' + document.getElementById('City').value + '+' + document.getElementById('State').value + '+' +  document.getElementById('PostalCode').value + '+' + document.getElementById('Country').value;

		//replace spaces with pluses
		address = address.replace(/ /ig,'+');

		address = address.replace(/\+\+\+/ig,'+');
		address = address.replace(/\+\+/ig,'+');
		
		showAddress(address);
	

}
//-->
</script>

<form action="index.php" method="post" name="adminForm" class="locator" onsubmit="return validate();">
<fieldset>
<h3><?php echo JText::_('Editor'); ?></h3>
<table class="adminform" width="100%">
<tr>
	<td width="10%" valign="top">
	<?php echo JText::_('LOCATOR_NAME'); ?>:
	</td>
	<td>
	<input id="name" name="name" value="<?php echo @$item->title;?>" /><span class="required">*</span>
	</td>
</tr>
<tr>
	<td width="10%" valign="top">
	<?php echo JText::_('LOCATOR_DESCRIPTION'); ?>:
	</td>
	<td>
<?php
		$editor = &JFactory::getEditor();
		// parameters : areaname, content, width, height, cols, rows
		echo $editor->display( 'description', @$item->description , '75%', '550', '75', '20',false) ;
		?>	
	</td>
</tr>
<?php 
if(isset($this->lists['tags'])){ ?>
<tr>
	<td width="10%" valign="top">
	<?php echo JText::_('LOCATOR_TAGS'); ?>:
	</td>
	<td>
		<?php echo $this->lists['tags'];?>
	</td>
</tr>
<?php 
} 
?>

<input type="hidden" value="1" name="geocode" id="geocode" <?php echo (@JRequest::getInt('id') > 0)?(""):("checked='checked'"); ?>/>

<?php 
//only show the fields if we have data
//if (strlen($item->lat) > 0) {?>

	<input id="lat" name="lat" value="<?php echo @$item->lat;?>" type="hidden" />
	<input id="lng" name="lng" value="<?php echo @$item->lng;?>" type="hidden" />
	
	<?php 

	$label = '';
	$value = '';
	//ensure we have at least one field so there's no empty span tag

			//declare the item type variables
			foreach ($item->fields as $field){ 
			
				foreach ($field as $key=>$val){
					
					$showfield = false;
					
					switch ($key){
						
						case "name":{
							$label = $val;
						}break;
						
						case 'value':{
							$value = $val;
						}
						
						
					}
				}
				
				

				?>
				<tr>
				<td><?php
				$lang_string = 'LOCATOR_FIELD_'.str_replace(" ","",$label);
				
				if(strpos(JText::_($lang_string),'LOCATOR_FIELD_') !== false){
					echo $label;	
				}else{
					echo JText::_($lang_string);
				}
				
				 ?></td>
				<td><input id="<?php echo str_replace(" ","",$label);?>" name="<?php echo str_replace(" ","",$label);?>" value="<?php echo $value; ?>" type="text" /></td>
				</tr>
				<?php
				
				$label = '';
				$value = '';
				
				
			}
			 
			 ?>
</table>

<?php if ($params->get( 'recaptcha',0 ) == 1){ ?>
<script type="text/javascript"
   src="https://api-secure.recaptcha.net/challenge?k=<?php echo $params->get( 'recaptcha_public'); ?>">
</script>
<noscript>
   <iframe src="http://api.recaptcha.net/noscript?k=<?php echo $params->get( 'recaptcha_public'); ?>" height="300" width="500" frameborder="0"></iframe><br>
   <textarea name="recaptcha_challenge_field" rows="3" cols="40">
   </textarea>
   <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
</noscript>
<?php }	 ?>
<input type="button" name="dosubmit" value="<?php echo JText::_('LOCATOR_SUBMIT'); ?>" onclick="submitbutton();" />
<input type="hidden" name="option" value="com_locator" />
<input type="hidden" name="layout" value="form" />
<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>" />
<input type="hidden" name="id" value="<?php echo JRequest::getInt('id'); ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="task" value="save" />
</fieldset>
</form>
<?php echo JHTML::_('behavior.keepalive'); ?>