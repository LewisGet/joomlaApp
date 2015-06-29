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
		 }
		 );
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

	}
	
	
	form.submit();
}
//-->
</script>

<form action="index.php" method="post" name="adminForm">
<fieldset>
<legend><?php echo JText::_('Editor'); ?></legend>
<table class="adminform" width="100%">
<tr>
	<td width="10%">
	Name:
	</td>
	<td>
	<input id="name" name="name" value="<?php echo @$item->name;?>" />
	</td>
</tr>
<tr>
	<td width="10%">
	Field Type
	</td>
	<td>
	<select name="type" id="type">
		<option <?php echo (@$item->type == 'text')?('selected="selected"'):("");?>>text</option>
		<option <?php echo (@$item->type == 'date')?('selected="selected"'):("");?>>date</option>
		<option <?php echo (@$item->type == 'link')?('selected="selected"'):("");?>>link</option>
		<option <?php echo (@$item->type == 'email')?('selected="selected"'):("");?>>email</option>
		<option <?php echo (@$item->type == 'image')?('selected="selected"'):("");?>>image</option>
		<option <?php echo (@$item->type == 'html')?('selected="selected"'):("");?>>html</option>
	</select>
	<!--<input id="type" name="type" size=12 value="<?php //echo @$item->type;?>" /><span class="required">*</span>-->
	</td>
</tr>
<tr>
	<td width="10%">
	Visitor Field <i>(Show on public form?)</i>
	</td>
	<td>
	
	<select id="visitor_field" name="visitor_field">
	<option value="0" <?php if (@$item->visitor_field == 0){ echo "SELECTED"; }?>>No</option>	
	<option value="1" <?php if (@$item->visitor_field == 1 || !isset($item)) { echo "SELECTED"; }?>>Yes</option>
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

<?php
if(@$item->id > 0){
?>
<tr>
	<td width="10%">
	Template Tag
	</td>
	<td>
	<input type="text" value="{<?php echo  preg_replace('/[^a-zA-Z0-9\s]/', '', str_replace(" ","",strtolower($item->name))); ?>}" readonly="readonly"/>
	</td>
</tr>

<?php } ?>

</table>
</fieldset>
<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid',''); ?>"/>
<input type="hidden" name="option" value="com_locator" />
<input type="hidden" name="id" value="<?php echo @$item->id; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="task" value="savefield" />
</form>
<?php echo JHTML::_('behavior.keepalive'); ?>
