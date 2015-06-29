<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<script language="javascript" type="text/javascript">

function submitbutton(pressbutton) {
	
	var form = document.adminForm;
	
	if (pressbutton == 'removetags') {
		
		if(confirm("Are you sure?")){
			submitform( pressbutton );
		}else{
			return;
		}
	}else{
		submitform( pressbutton );	
	}	
	
}
//-->
</script>

<form name="adminForm">
<table class="adminlist" cellspacing="1">
<?php

//display each directory entry
if(count($this->items)){

	?>
	<thead>
	<th width="5">
	#
	</th>
	<th width="5">
	ID
	</th>	
	<th width="5">
	<input type="checkbox" onclick="checkAll(<?php echo count($this->items); ?>);" value="" name="toggle"/>
	</th>
	<th>
	Title
	</th>
	<th>
	Child of
	</th>
	<th>
	Group
	</th>	
	<th>
	Ordering
	</th>	
	</thead>
	
	<?php
	
	
	//for ($i = $this->pagination->limitstart; $i < ($this->pagination->limit);  $i++){
	for ($i = 0; $i < $this->total;   $i++){
			
		
		$item = $this->items[$i];
		
		?>
		<tr>
		<td  align="center"><?php echo $i + 1; ?></td>
		<td  align="center"><?php echo $item->id; ?></td>
		<td align="center">
			<input type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $item->id;?>" name="cid[]" id="cb<?php echo $i; ?>"/>
		</td>
		<td  align="left">
		<a href="index.php?option=com_locator&task=edittag&id=<?php echo $item->id;?>&Itemid=<?php echo JRequest::getInt('Itemid',''); ?>"><?php echo $item->name; ?></a>
		</td>
		<td  align="left">
		<?php echo @$item->child_of; ?>
		</td>
		<td  align="left">
		<?php echo @$item->tag_group; ?>
		</td>
		<td  align="left" width="5%">
		<?php echo @$item->order; ?>
		</td>
		</tr>	
			
			
			<?php
			
			
		}
		
		?>

		<?php
}else{
	if(JRequest::getVar('task') == "search_zip"){
	?>
	<h4>No tags found.</h4>
	<?php
	}
}
?>


<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_locator"/>
<input type="hidden" name="task" value="managetags"/>
<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid',''); ?>"/>
</form>

<tfoot><td colspan="7">
<?php ?>
</td>
</tfoot>
</table>