<?php
/**
 * @version $Id: edit.php 99 2013-01-08 10:39:32Z michal $
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

defined('_JEXEC') or die();

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'item.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			<?php echo $this->form->getField('intro_desc')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_djcatalog2&view=item&layout=edit&id='.(int) $this->item->id); ?>"
	method="post" name="adminForm" id="item-form" class="form-validate"
	enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="span12 form-horizontal">
			<fieldset>
				<ul class="nav nav-tabs">
					<li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_DJCATALOG2_NEW') : JText::_('COM_DJCATALOG2_EDIT'); ?>
					</a></li>
					<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?>
					</a>
					</li>
					<li><a href="#images" data-toggle="tab"><?php echo JText::_('COM_DJCATALOG2_IMAGES'); ?>
					</a>
					</li>
					<li><a href="#files" data-toggle="tab"><?php echo JText::_('COM_DJCATALOG2_FILES'); ?>
					</a>
					</li>
					<li><a href="#attributes" data-toggle="tab"><?php echo JText::_('COM_DJCATALOG2_ATTRIBUTES'); ?>
					</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="details">
						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('name'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('name'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('alias'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('alias'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('published'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('published'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('featured'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('featured'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('id'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('id'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('cat_id'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('cat_id'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('categories'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('categories'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('producer_id'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('producer_id'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('price'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('price'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
								<label><?php echo JText::_('COM_DJCATALOG2_RELATED_ITEMS'); ?> </label>
							</div>
							<div class="controls">
							<?php if (empty($this->item->id) || ($this->item->id == 0)) { ?>
								<a class="btn" href="#"
									onclick="javascript:Joomla.submitbutton('item.apply')"> <?php echo JText::_('COM_DJCATALOG2_RELATED_ITEMS_SAVE_TO_ASSIGN'); ?>
								</a>
								<?php } else { ?>
								<a class="btn modal"
									rel="{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}"
									href="index.php?option=com_djcatalog2&amp;view=relateditems&amp;item_id=<?php echo $this->item->id; ?>&amp;tmpl=component">
									<?php echo JText::_('COM_DJCATALOG2_RELATED_ITEMS_ASSIGN'); ?>
								</a>
								<?php }?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('intro_desc'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('intro_desc'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('description'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('description'); ?>
							</div>
						</div>
					</div>

					<div class="tab-pane" id="publishing">
						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('created'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('created'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('created_by'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('created_by'); ?>
							</div>
						</div>
						
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('metatitle'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('metatitle'); ?></div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('metadesc'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('metadesc'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
							<?php echo $this->form->getLabel('metakey'); ?>
							</div>
							<div class="controls">
							<?php echo $this->form->getInput('metakey'); ?>
							</div>
						</div>
					</div>

					<div class="tab-pane" id="images">
					<?php echo DJCatalog2ImageHelper::renderInput('item',JRequest::getVar('id', null)); ?>
					</div>
					<div class="tab-pane" id="files">
					<?php echo DJCatalog2FileHelper::renderInput('item',JRequest::getVar('id', null)); ?>
					</div>
					<div class="tab-pane" id="attributes">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('group_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('group_id'); ?>
							</div>
						</div>
						<div id="itemAttributes"></div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
