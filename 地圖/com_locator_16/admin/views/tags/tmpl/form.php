<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$item = $this->items[0];

?>

<script language="javascript">
window.addEvent("domready"
		 , function( ){ 
			 var myselect = document.getElementById('marker_select');
			 
			 for (var i=0; i<myselect.options.length; i++){
				 if (myselect.options[i].value=='<?php echo @$item->marker; ?>'){
					 myselect.options[i].selected = true;
				  break;
				 }
				} 

			 document.getElementById('marker').value = '<?php echo @$item->marker; ?>';
			 });
</script>

<script language="javascript" type="text/javascript">

function submitbutton(pressbutton) {
	
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	if(document.getElementById('order').value == ""){
		alert('A numeric Ordering/Priority is required');		
		return false;
	}
	
	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}
	
	
	form.submit();
}
//-->
</script>

<form action="index.php" method="post" name="adminForm">
<fieldset>
<legend><?php echo JText::_('Editor'); ?></legend>
<table class="adminform" width="100%">
<tr><td width="50%" valign="top">
<table class="adminform" width="100%">
<tr>
	<td width="10%">
	Name:
	</td>
	<td>
	<input id="name" name="name" value="<?php echo @$item->name;?>" /><span class="star">&nbsp;*</span>
	<input id="description" name="description" type="hidden" value="<?php echo @$item->description;?>" />
	</td>
</tr>
<tr>
	<td width="10%">
	Marker:
	</td>
	<td>
	<select id="marker_select" name="marker_select" value="<?php echo @$item->marker;?>" onchange="document.getElementById('marker').value = document.getElementById('marker_select').options[document.getElementById('marker_select').selectedIndex].value"; >
		<option value="">Default Marker</option>
		<option value="http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png">Red Marker with Dot</option>
		<option value="http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png">Blue Marker with Dot</option>
		<option value="http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png">Yellow Marker with Dot</option>
		<option value="http://www.google.com/intl/en_us/mapfiles/ms/micons/green-dot.png">Green Marker with Dot</option>
		<option value="http://www.google.com/intl/en_us/mapfiles/ms/micons/orange-dot.png">Orange Marker with Dot</option>
		<option value="http://www.google.com/intl/en_us/mapfiles/ms/micons/purple-dot.png">Purple Marker with Dot</option>		
		<option value="http://www.google.com/intl/en_us/mapfiles/ms/micons/red.png">Red Marker</option>
		<option value="http://www.google.com/intl/en_us/mapfiles/ms/micons/blue.png">Blue Marker</option>
		<option value="http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow.png">Yellow Marker</option>
		<option value="http://www.google.com/intl/en_us/mapfiles/ms/micons/green.png">Green Marker</option>
		<option value="http://www.google.com/intl/en_us/mapfiles/ms/micons/orange.png">Orange Marker</option>
		<option value="http://www.google.com/intl/en_us/mapfiles/ms/micons/purple.png">Purple Marker</option>				
	</select>
	
	</td>
</tr>


<tr>
	<td width="10%">
	Customize Marker:
	</td>
	<td>
		<input id="marker" name="marker"  value="<?php echo @$item->marker;?>" />	
	</td>
</tr>


<tr>
	<td width="10%">
	Customized Marker Shadow:
	</td>
	<td>
<input id="marker_shadow" name="marker_shadow" value="<?php echo @$item->marker_shadow;?>" />
	</td>
</tr>

<tr>
	<td width="10%">
	Tag Group:
	</td>
	<td>
	<input id="tag_group" name="tag_group" value="<?php echo @$item->tag_group;?>" />
	</td>
</tr>
<tr>
	<td width="10%">
	Tag Group Order:
	</td>
	<td>
	<input size="3" id="tag_group_order" name="tag_group_order" value="<?php echo @$item->tag_group_order;?>" />
	</td>
</tr>

<tr>
	<td width="10%">
	Parent Tag:
	</td>
	<td>
	 <?php if(!isset($this->lists['child_of'])){echo '<i>No tags</i>'; }else{echo $this->lists['child_of'];}?>
	</td>
</tr>
	


<tr>
	<td width="10%">
	Show in front-end location form?:
	</td>
	<td>
	<select id="user_tag" name="user_tag">
		<option value="0" <?php if (@$item->user_tag == 0){ echo "SELECTED"; }?>>No</option>
		<option value="1" <?php if (@$item->user_tag == 1 || !isset($item)) { echo "SELECTED"; }?>>Yes</option>
	</select>	
	</td>
</tr>

<tr>
	<td width="10%">
	Ordering/Priority
	</td>
	<td>
	<input id="order" name="order" size=12 value="<?php echo ($item->order > 0)?($item->order):($this->lists['next_order']);?>" /><span class="required">*</span>
	</td>
</tr>
</table>
</td>
<td valign="top"  valign="top" align="left" width="50%">
<table class="adminform" width="100%"><tr><td>
<h2>Marker Gallery</h2>
<i>Click to select the icon for this tag.</i>
<div id="thumbnails" style="height:200px; overflow:auto;">
<?php

$path = JPATH_SITE . DS . 'components' . DS. 'com_locator' . DS.'assets'.DS. 'icons';

$url = JURI::root() . 'components' . DS. 'com_locator' . DS.'assets'.DS. 'icons';

if (is_dir($path))
{
    $dh = opendir($path);
 
    while ($file = readdir($dh))
    {
    	
    	if ($file != '.' && $file != '..'  && strpos($file,'.png') !== false) {
    		?>
    		<a href="javascript:void(0);" onclick="document.getElementById('marker').value='<?php echo $url . '/' . $file; ?>'"><img src="<?php echo $url . '/' . $file; ?>" border="0" /></a>
    		<?php
    		
    	}	
    }
}

?>
</div>
</td></tr></table>
</td></tr></table>
</fieldset>
<input type="hidden" name="option" value="com_locator" />
<input type="hidden" name="id" value="<?php echo @$item->id; ?>" />
<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid',''); ?>"/>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="task" value="savetag" />
</form>
<?php echo JHTML::_('behavior.keepalive'); ?>
