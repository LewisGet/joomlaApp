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

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<form action="index.php?option=com_j2store&view=taxprofiles" method="post" name="adminForm" id="adminForm">
<table>
<tr>
	<td align="left" width="100%">
		<?php echo JText::_( 'J2STORE_FILTER_SEARCH' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'J2STORE_FILTER_GO' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'J2STORE_FILTER_RESET' ); ?></button>
	</td>	
</tr>
</table>

<div id="editcell" class="width-60">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'J2STORE_NUM' ); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th class="title">
				<?php echo JHTML::_('grid.sort',  'J2STORE_TAX_PROFILE_NAME', 'a.taxprofile_name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>			
			<th class="title">
				<?php echo JHTML::_('grid.sort',  'J2STORE_TAX_PROFILE_PERCENT', 'a.tax_percent', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',  'J2STORE_TAX_PROFILE_STATE', 'a.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="5">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];

		$link 	= JRoute::_( 'index.php?option=com_j2store&view=taxprofiles&task=edit&cid[]='. $row->id );

		//$checked 	= JHTML::_('grid.checkedout',   $row, $i );
		$checked = JHTML::_('grid.id', $i, $row->id );
		
		$published 	= JHTML::_('grid.published', $row, $i );
	
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'J2STORE_TAX_PERCENT_VIEW' );?>::<?php echo $this->escape($row->taxprofile_name); ?>">
				<a href="<?php echo $link ?>" >
				<?php echo $this->escape($row->taxprofile_name); ?>
				</a>
				</span>
			</td>
		
			<td align="center">
				<?php echo $row->tax_percent; ?>
			</td>
			
			<td align="center">
				<?php echo $published;?>
			</td>			
			
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
</div>

	<input type="hidden" name="option" value="com_j2store" />
	<input type="hidden" name="view" value="taxprofiles" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<div class="clr"></div>

