<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<form name="adminForm" action="<?php echo JRoute::_( 'index.php' );?>"  method="post">
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
	<?php echo JHTML::_( 'grid.sort', 'ID', 'search_id', $this->lists['order_Dir'], $this->lists['order']); ?>
	</th>
	<th width="5">
	<input type="checkbox" onclick="checkAll(<?php echo count($this->items); ?>);" value="" name="toggle"/>
	</th>
	<th>
	<?php echo JHTML::_( 'grid.sort', 'Postal Code', 'postal_code', $this->lists['order_Dir'], $this->lists['order']); ?>
	</th>
	<th >
	<?php echo JHTML::_( 'grid.sort', 'City', 'city', $this->lists['order_Dir'], $this->lists['order']); ?>
	</th>	
	<th >
	<?php echo JHTML::_( 'grid.sort', 'State', 'state', $this->lists['order_Dir'], $this->lists['order']); ?>
	</th>
	<th >
	<?php echo JHTML::_( 'grid.sort', 'Country', 'country', $this->lists['order_Dir'], $this->lists['order']); ?>
	</th>
	<th >
	<?php echo JHTML::_( 'grid.sort', 'Keyword', 'keyword', $this->lists['order_Dir'], $this->lists['order']); ?>
	</th>
	<th >
	<?php echo JHTML::_( 'grid.sort', 'Tag', 'tag', $this->lists['order_Dir'], $this->lists['order']); ?>
	</th>
	<th>
	<?php echo JHTML::_( 'grid.sort', 'IP', 'IP', $this->lists['order_Dir'], $this->lists['order']); ?>
	</th>
	<th>
	<?php echo JHTML::_( 'grid.sort', 'Radius', 'radius', $this->lists['order_Dir'], $this->lists['order']); ?>
	</th>					
	</thead>
	<?php
	for ($i = 0; $i < ($this->pagination->limit);  $i++){
	
		if (($this->pagination->limitstart + $i) >= $this->total) {
			 break;  
		}
		
		$item = $this->items[$i];
		
	?>
	<tr>
		<td><?php echo $i + 1; ?></td>
		<td><?php echo $item->search_id; ?></td>
		<td></td>
		<td><?php echo $item->postal_code; ?></td>
		<td><?php echo $item->city; ?></td>
		<td><?php echo $item->state; ?></td>
		<td><?php echo $item->country; ?></td>
		<td><?php echo $item->keyword; ?></td>
		<td><?php echo $item->tag; ?></td>
		<td><?php echo $item->IP; ?></td>
		<td><?php echo $item->radius; ?></td>
		
	</tr>	
	<?php 	
	}//end for each item
	
	
}else{
	?>
	<h4>No searches have been recorded</h4>
	<?php 
}
	?>
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="search"/>
<input type="hidden" name="option" value="com_locator"/>
<input type="hidden" name="task" value="<?php echo JRequest::getString('task'); ?>"/>

<tfoot><td colspan="11">
<?php echo $this->pagination->getListFooter(); ?>
</td>
</tfoot>
</table>
</form>