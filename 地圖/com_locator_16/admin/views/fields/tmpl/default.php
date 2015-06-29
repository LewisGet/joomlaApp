<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<form name="adminForm">
<table class="adminlist" cellspacing="1">
<?php
//display each directory entry
if(count($this->items)){

	?>
	<thead>
	<th width="5">
	ID
	</th>	
	<th width="5">
	&nbsp;
	</th>
	<th>
	Title
	</th>
	<th>
	Type
	</th>
	<th>
	Template Code
	</th>			
	<th>
	Ordering
	</th>	
	
	<th>
	System Field
	</th>
	<th>
	Visitor Field
	</th>		
	</thead>
	
	<?php
	//for ($i = $this->pagination->limitstart; $i < ($this->pagination->limit);  $i++){
	for ($i = 0; $i < $this->total;   $i++){

		$item = $this->items[$i];
		
		?>
		<tr>
		<td align="center"><?php echo $item->id; ?></td>
		<td align="center">
			<?php
			if($item->iscore == 1){
				?>
				<input type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $item->id;?>" name="cid[]" id="cb<?php echo $i; ?>" style="display:none;"/>
				<?php
				}else{
			?>
			<input type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $item->id;?>" name="cid[]" id="cb<?php echo $i; ?>"/>
			<?php } ?>
		</td>
		<td align="left">
		<?php
			if($item->iscore == 1){
				?>
				<?php echo $item->name; ?>
				<?php
				}else{
			?>
			<a href="index.php?option=com_locator&task=editfield&id=<?php echo $item->id;?>&Itemid=<?php echo JRequest::getInt('Itemid'); ?>"><?php echo $item->name; ?></a>
			<?php } ?>
		
		</td>
		<td  align="left">
		<?php echo $item->type; ?>
		</td>		
		<td  align="left">
		{<?php echo preg_replace('/[^a-zA-Z0-9\s]/', '', str_replace(" ","",strtolower($item->name)));  ?>}
		</td>		
		<td align="left" width="5%">
		<?php echo $item->order; ?>
		</td>
		<td align="center" width="5%">
			
		<?php if($item->iscore == 1){
			?>
			<img width="16" height="16" border="0" alt="Published" src="<?php echo JURI::root(true); ?>/administrator/components/com_locator/images/publish_g.png"/>
			<?php }?>

		</td>	

		 
			<td align="center" width="5%">
			<span class="editlinktip hasTip">
		<?php if($item->visitor_field == 1){
			?>
			<a onclick="return listItemTask('cb<?php echo $i; ?>','unset_userfield')"><img width="16" height="16" border="0" alt="Published" src="<?php echo JURI::root(true); ?>/administrator/components/com_locator/images/publish_g.png"/></a>
			<?php }else{
			?>	
			<a onclick="return listItemTask('cb<?php echo $i; ?>','set_userfield')"><img width="16" height="16" border="0" alt="Published" src="<?php echo JURI::root(true); ?>/administrator/components/com_locator/images/publish_x.png"/></a>
			<?php
			} ?>
		</span>
		</td>	
		</tr>	

			<?php
			
			
		}
		
		?>

		<?php
}else{
	
	?>
	<h4>No results found.</h4>
	<?php
}
?>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_locator"/>
<input type="hidden" name="task" value="managetags"/>
<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid',''); ?>"/>
</form>
<tfoot><td colspan="9">
<?php ?>
</td>
</tfoot>
</table>