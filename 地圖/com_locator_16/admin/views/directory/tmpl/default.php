<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$ tc
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<form name="adminForm" action="<?php echo JRoute::_( 'index.php' );?>"  method="post">
<span class="label">Keyword:</span><input type="text" name="keyword" id="keyword" class="inputbox" value="<?php echo JRequest::getString('keyword');?>"/>
<?php
if(isset( $this->lists['tags'])){ ?>
<span class="label">Tag:</span><?php echo $this->lists['tags']; 
}
?>
<?php
if(isset( $this->lists['city'])){ ?>
<span class="country">City:</span><?php echo $this->lists['city']; 
}
?>
<?php
if(isset( $this->lists['states'])){ ?>
<span class="country">State:</span><?php echo $this->lists['states']; 
}
?>
<?php
if(isset( $this->lists['country'])){ ?>
<span class="country">Country:</span><?php echo $this->lists['country']; 
}

$params = &JComponentHelper::getParams( 'com_locator' );
?>
<span class="label">Published:</span>
<select class="inputbox" name="published" id="published">
	<option value="-1">Please Select...</option>
	<option value="1" <?php echo (JRequest::getInt('published',-1) == 1)?("selected"):('');?> >Published</option>
	<option value="0" <?php echo (JRequest::getInt('published',-1) == 0)?("selected"):('');?> >Unpublished</option>
</select>

<span class="label">Geocoded:</span>
<select class="inputbox" name="geocoded" id="geocoded">
	<option value="-1">Please Select...</option>
	<option value="1" <?php echo (JRequest::getInt('geocoded',-1) == 1)?("selected"):('');?> >Geocoded</option>
	<option value="0" <?php echo (JRequest::getInt('geocoded',-1) == 0)?("selected"):('');?> >Not Geocoded</option>
</select>

<input type="button" id="cmd" name="cmd" value="search" onclick="document.adminForm.task.value='';document.adminForm.limitstart.value=0;document.adminForm.submit();"/>
<br />
<br />
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
	
	<?php echo JHTML::_( 'grid.sort', 'ID', 'id', $this->lists['order_Dir'], $this->lists['order']); ?>
	</th>
	<th width="5">
	<input type="checkbox" onclick="checkAll(<?php echo count($this->items); ?>);" value="" name="toggle"/>
	</th>
	<th>
	<?php echo JHTML::_( 'grid.sort', 'Title', 'Name', $this->lists['order_Dir'], $this->lists['order']); ?>
	</th>
	<th width="25%">
	Tag(s)
	</th>	
	<th width="1%">
	<?php echo JHTML::_( 'grid.sort', 'Published', 'published', $this->lists['order_Dir'], $this->lists['order']); ?>
	</th>
	<th width="1%">
	<?php echo JHTML::_( 'grid.sort', 'Geocoded', 'lng', $this->lists['order_Dir'], $this->lists['order']); ?>
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
		<td align="center"><?php echo $this->pagination->limitstart + $i + 1; ?></td>
		<td align="center"><?php echo $item->id; ?></td>
		<td align="center">
			<input type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $item->id;?>" name="cid[]" id="cb<?php echo $i; ?>"/>
		</td>
		
		<td align="left">
		<a href="index.php?option=com_locator&task=edit&id=<?php echo $item->id;?>&Itemid=<?php echo JRequest::getInt('Itemid'); ?>"><?php echo $item->title; ?>
		<?php
			echo $this->formatItem($item,$params,'admin_template');
		?>
		</a>
		</td>
		<td align="center">
		<?php 
		
		if(isset($item->taglist)){
			echo rtrim(trim($item->taglist),",");
		}	
		?>
		</td>		
		<td align="center">
		<span class="editlinktip hasTip">
		<?php if($item->published == 1){
			?>
			<a onclick="return listItemTask('cb<?php echo $i; ?>','unpublish')" href="javascript:void(0);" class="jgrid">
			<img width="16" height="16" border="0" alt="Published" src="<?php echo JURI::root(true); ?>/administrator/components/com_locator/images/publish_g.png"/>
			</a>
			<?php }else{
				?>
			<a onclick="return listItemTask('cb<?php echo $i; ?>','publish')" href="javascript:void(0);">
			<img width="16" height="16" border="0" alt="Published" src="<?php echo JURI::root(true); ?>/administrator/components/com_locator/images/publish_x.png"/>
			<?php }?>
			</a>
		</span>
		</td>
		<td align="center">
		<span class="editlinktip hasTip">
		<?php if(abs($item->lat) > 0){
			?>
			
			<img width="16" height="16" border="0" alt="Published" src="<?php echo JURI::root(true); ?>/administrator/components/com_locator/images/publish_g.png"/>
			<?php }else{
				?>
					
			<img width="16" height="16" border="0" alt="Published" src="<?php echo JURI::root(true); ?>/administrator/components/com_locator/images/publish_x.png"/>		
			<?php }?>
		</span>
		</td>

		</tr>	

			<?php
			
			
		}
		
		?>

		<?php
}else{
	?>
	<h4>No locations found.</h4>
	<?php
}
?>
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="directory"/>
<input type="hidden" name="option" value="com_locator"/>
<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid',''); ?>"/>
<input type="hidden" name="task" value="<?php echo JRequest::getString('task'); ?>"/>

<tfoot><td colspan="7">
<?php $list = $this->pagination->getListFooter();

$list = str_ireplace('<option value="0" >All</option>','<option value="2500" >2500</option><option value="1000">1000</option><option value="0" >'.JText::_('All').'</option>',$list);

$list = str_ireplace('<option value="0">All</option>','<option value="2500">2500</option><option value="1000">1000</option><option value="0" >'.JText::_('All').'</option>',$list);


echo $list;
?>
</td>
</tfoot>
</table>
</form>

