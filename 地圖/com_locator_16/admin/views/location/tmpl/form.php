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

$gmap_base_tld = $params->get( 'gmap_base_tld','US');

global $mainframe;
$doc =& JFactory::getDocument();

$doc->addScript("http://maps.google.com/maps/api/js?sensor=false");

$item = $this->items[0];


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

?>

<script language="javascript" type="text/javascript">

function showAddress(address) {

	var form = document.adminForm;
	
	try{
		
		var geocoder = new google.maps.Geocoder();
		
		var TLD;
		
		
		if(document.getElementById('tld') && document.getElementById('tld').value != ''){
			TLD = document.getElementById('tld').value;
		}else{
			TLD = '<?php echo $gmap_base_tld; ?>';	
		}
	
		geocoder.geocode( { 'address': address,'region': TLD},function(results,status){
	
			if (status == google.maps.GeocoderStatus.OK) {
		
			  		document.getElementById('lat').value = results[0].geometry.location.lat();
			  		document.getElementById('lng').value = results[0].geometry.location.lng();
			  		form.submit();

			}else{
			  
			        alert("Address was not geocoded: No valid address match found.  Status: " + status);
			       	form.submit();				
			}
			
		});
		
	}catch(e) {
		 form.submit();
	}
}

//1.6

submitbutton = function(pressbutton)
{
        if (pressbutton == '')
        {
                return false;
        }
        else
        {
        
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		
		//validate the entry
		if(document.getElementById('name').value == ''){
			alert('<?php echo JText::_('LOCATOR_NAME_INVALID'); ?>'); 
			return false;
		}
		
		if((document.getElementById('geocode').checked == true && document.getElementById('geocode').type =='checkbox') ||( document.getElementById('geocode').value == 1 && document.getElementById('geocode').type =='hidden')){
			//geocode the address provided
			var address = document.getElementById('Address').value + '+' + document.getElementById('City').value + '+' + document.getElementById('State').value + '+' +  document.getElementById('PostalCode').value + '+' + document.getElementById('Country').value;
	
			//replace spaces with pluses
			address = address.replace(/^\s+|\s+$/g,'');
			address = address.replace(/ /ig,'+');
	
			address = address.replace(/\+\+\+/ig,'+');
			address = address.replace(/\+\+/ig,'+');
			
			showAddress(address);
			
		}else{
			form.submit();
		}
    }

}

if(typeof(Joomla) != 'undefined'){
	Joomla.submitbutton	= submitbutton;
}

//-->
</script>

<form action="index.php" method="post" name="adminForm">
<fieldset>
<legend><?php echo JText::_('Editor'); ?></legend>
<table class="adminform" width="100%">
<tr>
	<td width="10%" valign="top">
	<?php echo JText::_('Title'); ?>:
	</td>
	<td>
	<input id="name" name="name" value="<?php echo @$item->title;?>" /><span class="required">*</span>
	</td>
</tr>
<tr>
	<td width="10%" valign="top">
	<?php echo JText::_('Description'); ?>:
	</td>
	<td>
<?php
		if(JRequest::getInt('framed',0) == 0 && LocatorModelDirectory::hasAdmin()){
			$editor = &JFactory::getEditor();
			// parameters : areaname, content, width, height, cols, rows
			echo $editor->display( 'description', @$item->description , '75%', '550', '75', '20'  ) ;
		}else{
			?>
			<textarea id="description" name="description" type="text" ><?php echo @$item->description; ?></textarea>
			<?php
		}
?>	
	</td>
</tr>
<tr>
	<td width="10%" valign="top">
	Tags
	</td>
	<td>
		<?php echo $this->lists['tags'];?>
	</td>
</tr>
<?php if(JRequest::getInt('framed',0) == 0){ ?>
<tr>
	<td width="10%">
	Geocode on save:
	</td>
	<td>
	<input type="checkbox" value="1" name="geocode" id="geocode" <?php echo  (@$item->id > 0)?(""):("checked='checked'"); ?>/>
	</td>
</tr>
<tr>
	<td width="10%">
	Published?:
	</td>
	<td>
	<input type="checkbox" value="1" name="published" id="published" <?php echo  (@$item->published > 0)?("checked='checked'"):(""); ?>/>
	</td>
</tr>
<tr>
	<td width="10%">
	Latitude:
	</td>
	<td>
	<input id="lat" name="lat" value="<?php echo @$item->lat;?>" />
	</td>
</tr>	
<tr>
	<td width="10%">
	Longitude:
	</td>
	<td>
	<input id="lng" name="lng" value="<?php echo @$item->lng;?>" />
	</td>
</tr>	
<?php 
}else{
	?>
	<input type="hidden" value="1" name="published" id="published" value="<?php echo  (@$params->get('publishautomatically',0) > 0)?("1"):("0"); ?>"/>
	<input type="hidden" value="1" name="geocode" id="geocode" value="1"/>
	<input type="hidden" id="lat" name="lat" value="<?php echo @$item->lat;?>" />
	<input type="hidden" id="lng" name="lng" value="<?php echo @$item->lng;?>" />
	<?php
	
}

	$label = '';
	$value = '';
	$type = '';
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
						}break;
						
						case 'type':{
							$type = $val;
						}break;
					}
				}

				/**
				 * This is an example of using a pulling a drop-down list instead of an plain-text field.
				if($label == "State"){
				
					$db = JFactory::getDBO();
					$db->setQuery("SELECT state_name as value,state_name as text from #__donate_states");

					$options = $db->loadObjectList();
					$list  = JHTML::_('select.genericlist', $options, 'State', '', 'value', 'text', $value);
				
					?>
					
					<tr>
					<td>State:</td><td><?php echo $list; ?> </td></tr>
					<?php
				}else{
				*/
				
				switch ($type) {
					case 'html':
					
					?>
						<tr>
						<td><?php echo JText::_('' . str_replace(" ","",$label)); ?> </td>
						<td><textarea id="<?php echo str_replace(" ","",$label);?>" id="<?php echo str_replace(" ","",$label);?>" name="<?php echo str_replace(" ","",$label);?>" type="text" ><?php echo $value; ?></textarea></td>
						</tr>
						<?php
						
					break;
				
					default:
						
						?>
						<tr>
						<td><?php echo JText::_('' . str_replace(" ","",$label)); ?> </td>
						<td><input id="<?php echo str_replace(" ","",$label);?>" id="<?php echo str_replace(" ","",$label);?>" name="<?php echo str_replace(" ","",$label);?>" value="<?php echo $value; ?>" type="text" /></td>
						</tr>
						<?php
				
						break;
				}

				
				/*}*/
				
				$label = '';
				$value = '';
				$type = '';
				
			}
			 ?>
			 
</table>

<?php if ($params->get( 'recaptcha',0 ) == 1 && JRequest::getInt('framed',0) == 1){ ?>
<script type="text/javascript"
   src="https://www.google.com/recaptcha/api/challenge?k=<?php echo $params->get( 'recaptcha_public'); ?>">
</script>
<noscript>
   <iframe src="https://www.google.com/recaptcha/api/noscript?k=<?php echo $params->get( 'recaptcha_public'); ?>" height="300" width="500" frameborder="0"></iframe><br>
   <textarea name="recaptcha_challenge_field" rows="3" cols="40">
   </textarea>
   <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
</noscript>
<?php }	 ?>

<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid',''); ?>"/>
<input type="hidden" name="option" value="com_locator" />

<input type="hidden" name="id" value="<?php echo JRequest::getInt('id'); ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="task" value="save" />

<?php 
if(JRequest::getInt('framed',0) == 1){
?>
<input type="button" name="dosubmit" value="<?php echo JText::_('LOCATOR_SUBMIT'); ?>" onclick="submitbutton();" />
<input type="hidden" name="layout" value="form" />
<?php
}?>


<?php if(strlen(JRequest::getWord('tmpl')) > 0){ ?>
<input type="hidden" value="<?php echo (isset($_REQUEST['tmpl']))?($_REQUEST['tmpl']):(""); ?>" name="tmpl" />
<?php } ?>

<?php if(JRequest::getInt('framed') == 1){ ?>
<input type="hidden" value="1" name="framed" />
<?php } ?>

</fieldset>
</form>
<?php echo JHTML::_('behavior.keepalive'); ?>
