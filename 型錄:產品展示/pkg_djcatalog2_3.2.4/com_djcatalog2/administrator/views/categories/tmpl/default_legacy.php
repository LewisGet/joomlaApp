<?php
/**
 * @version $Id: default_legacy.php 105 2013-01-23 14:05:57Z michal $
 * @package DJ-Catalog2
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Michal Olczyk - michal.olczyk@design-joomla.eu
 *
 * DJ-Catalog2 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-Catalog2 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-Catalog2. If not, see <http://www.gnu.org/licenses/>.
 *
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder == 'a.ordering';
?>
<form action="<?php echo JRoute::_('index.php?option=com_djcatalog2&view=categories');?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th width="75" align="center">
					<?php echo JText::_('COM_DJCATALOG2_IMAGE'); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_DJCATALOG2_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'categories.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php 
		$i = 0;
		$n = count($this->items);
		foreach ($this->items as $item) :
			$item->max_ordering = 0;
			$ordering	= ($listOrder == 'a.ordering');
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td align="center">
					<?php 
					if ($item->item_image) { ?><img alt="<?php echo $item->image_caption; ?>" src="<?php echo DJCatalog2ImageHelper::getImageUrl($item->item_image,'thumb'); ?>"/><?php }
					else { ?><img src="<?php echo str_replace('/administrator', '', JURI::base()).'components/com_djcatalog2/assets/images/noimage.jpg'; ?>" alt="" /><?php }?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'categories.', $canCheckin); ?>
						<?php echo str_repeat('<span class="gi">|&mdash;</span>',count($this->categories->get($item->id)->getPath())-1); ?>
						<?php echo $item->name; ?>
					<?php else : ?>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_DJCATALOG2_EDIT_CATEGORY_TOOLTIP' );?>::<?php echo $this->escape($item->name); ?>">
						<?php echo str_repeat('<span class="gi">|&mdash;</span>',count($this->categories->get($item->id)->getPath())-1); ?>
						<a href="<?php echo JRoute::_('index.php?option=com_djcatalog2&task=category.edit&id='.$item->id);?>">
							<?php echo $item->name; ?></a>
						</span>
					<?php endif; ?>
					
					<p class="smallsub"><?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'categories.', true, 'cb'	); ?>
				</td>
				<td class="order">
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->parent_id == 0 || $item->parent_id == @$this->items[$i-1]->parent_id), 'categories.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->parent_id == 0 || $item->parent_id == @$this->items[$i+1]->parent_id), 'categories.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->parent_id == 0 || $item->parent_id == @$this->items[$i-1]->parent_id), 'categories.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->parent_id == 0 || $item->parent_id == @$this->items[$i+1]->parent_id), 'categories.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php $i++; endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
