<?php
/*------------------------------------------------------------------------
# com_j2store - J2 Store v 1.0
# ------------------------------------------------------------------------
# author    Sasi varna kumar - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2012 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://j2store.org
# Technical Support:  Forum - http://j2store.org/forum/index.html
-------------------------------------------------------------------------*/


//no direct access
defined('_JEXEC') or die('Restricted access'); 
JHTML::_('stylesheet', 'style.css', 'administrator/components/com_j2store/css/');
 $state = @$this->state; 
 $items = @$this->items; 
 $row = @$this->row;
 $action = JRoute::_( 'index.php?option=com_j2store&view=products&task=setattributes&tmpl=component&id='.$row->id);
 require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'popup.php');
 ?>
                            
<h1 style="margin-left: 2%; margin-top: 2%;"><?php echo JText::_( "J2STORE_PA_SET_ATTRIB_FOR" ); ?>: <?php echo $row->title; ?></h1>

<form action="<?php echo $action; ?>" method="post" name="adminForm" enctype="multipart/form-data">

	<?php //echo TiendaGrid::pagetooltip( JRequest::getVar('view') ); ?>
	
<div class="note" style="width: 96%; margin-left: auto; margin-right: auto;">

    <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('J2STORE_PA_ADD_NEW_ATTRIB'); ?></div>
    <div style="float: right;">
        <button onclick="document.getElementById('task').value='createattribute'; document.adminForm.submit();"><?php echo JText::_('J2STORE_PA_CREATE_ATTRIB'); ?></button>
    </div>
    <div class="reset"></div>
    
	<table class="adminlist">
    	<thead>
    	<tr>
    		<th><?php echo JText::_( "J2STORE_PA_NAME" ); ?></th>
    	</tr>
    	</thead>
    	<tbody>
    	<tr>
    		<td style="text-align: center;">
    			<input id="productattribute_name" name="productattribute_name" value="" />
    		</td>
    	</tr>
    	</tbody>
	</table>
</div>


<div class="note_green" style="width: 96%; margin-left: auto; margin-right: auto;">
    <div style="float: left; font-size: 1.3em; font-weight: bold; height: 30px;"><?php echo JText::_('J2STORE_PA_CURRENT_ATTRIBS'); ?></div>
    <div style="float: right;">
        <button onclick="document.getElementById('task').value='saveattributes'; document.adminForm.toggle.checked=true; checkAll(<?php echo count( @$items ); ?>); document.adminForm.submit();"><?php echo JText::_('J2STORE_SAVE_CHANGES'); ?></button>
    </div>
    <div class="reset"></div>
    
	<table class="adminlist" style="clear: both;">
		<thead>
            <tr>
                <th style="width: 20px;">
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( @$items ); ?>);" />
                </th>
                <th style="text-align: left;">
					<?php echo JHTML::_('grid.sort',  'J2STORE_PA_NAME', 'a.productattribute_name',  $this->lists['order_Dir'], $this->lists['order'] ); ?>
                </th>
                <th style="text-align: left;">
					<?php echo JHTML::_('grid.sort',  'J2STORE_PA_PRODUCT_ID', 'a.product_id',  $this->lists['order_Dir'], $this->lists['order'] ); ?>					
                </th>
                <th style="width: 100px;">
					<?php echo JHTML::_('grid.sort',  'J2STORE_ORDERING', 'a.ordering',  $this->lists['order_Dir'], $this->lists['order'] ); ?>					
                </th>
				<th style="width: 100px;">
				</th>
            </tr>
		</thead>
        <tbody>
	
		<?php $i=0; $k=0; ?>
        <?php foreach (@$items as $item) : 
        $checked = JHTML::_('grid.id', $i, $item->productattribute_id);
	    ?>
            <tr class='row<?php echo $k; ?>'>
				<td style="text-align: center;">
					<?php
					 //echo JHTML::_('grid.checkedout',   $item, $i );
					echo $checked;
					?>
				</td>
				<td style="text-align: left;">
					<input type="text" name="name[<?php echo $item->productattribute_id; ?>]" value="<?php echo $item->productattribute_name; ?>" />
					[<?php echo J2StorePopup::popup( "index.php?option=com_j2store&view=products&task=setattributeoptions&id=".$item->productattribute_id."&tmpl=component", JText::_( 'J2STORE_PA_SET_OPTIONS' ) ); ?>]
				</td>
				<td><?php echo $item->product_id; ?></td>
				<td style="text-align: center;">
					<input type="text" name="ordering[<?php echo $item->productattribute_id; ?>]" value="<?php echo $item->ordering; ?>" size="10" />
				</td>
				<td style="text-align: center;">
					[<a href="index.php?option=com_j2store&view=products&task=deleteattributes&product_id=<?php echo $row->id; ?>&cid[]=<?php echo $item->productattribute_id; ?>&return=<?php echo base64_encode("index.php?option=com_j2store&view=products&task=setattributes&id={$row->id}&tmpl=component"); ?>">
						<?php echo JText::_( "J2STORE_PA_DELETE_ATTRIB" ); ?>	
					</a>
					]
				</td>
			</tr>
			<?php $i=$i+1; $k = (1 - $k); ?>
			<?php endforeach; ?>
			
			<?php if (!count(@$items)) : ?>
			<tr>
				<td colspan="10" align="center">
					<?php echo JText::_('J2STORE_NO_ITEMS_FOUND'); ?>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="20">
					<?php echo @$this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="task" id="task" value="setattributes" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

</div>
</form>
