<?php
/**
 * @version $Id: default.php 99 2013-01-08 10:39:32Z michal $
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

defined('_JEXEC') or die('Restricted access'); 
JHtml::_('behavior.tooltip');
?>
<div>
<div id="j-sidebar-container" class="span2">
<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span5 form-horizontal">

		<fieldset>
		<div class="control-group">
			<div class="control-label">
				<label for="djc_start_deleting" class="hasTip" title="<?php echo JText::_('COM_DJCATALOG2_THUMBNAILS_RECREATOR_LABEL_DESC'); ?>"><?php echo JText::_('COM_DJCATALOG2_THUMBNAILS_RECREATOR_LABEL'); ?></label>
			</div>
			<div class="controls">
				<div class="djc_thumbrecreator">
					<button disabled="disabled" class="button btn"
						id="djc_start_recreation">
						<?php echo JText::_('COM_DJCATALOG2_THUMBNAILS_RECREATOR_BUTTON'); ?>
					</button>
					<div style="clear: both" class="clr"></div>
					<div id="djc_progress_bar_outer" class="progress">
						<div id="djc_progress_bar" class="bar"></div>
					</div>
					<div id="djc_progress_percent">0%</div>
				</div>
			</div>
		</div>
		
		<?php 
		$files = JFolder::files(DJCATIMGFOLDER.DS.'custom', '.', false, false, array('index.html', '.svn', 'CVS', '.DS_Store', '__MACOSX')); 
		$file_count = count($files);
		?>
		
		<div class="control-group">
			<div class="control-label">
			<label for="djc_start_deleting" class="hasTip" title="<?php echo JText::_('COM_DJCATALOG2_IMAGES_DELETE_LABEL_DESC'); ?>"><?php echo JText::_('COM_DJCATALOG2_IMAGES_DELETE_LABEL'); ?></label>
			</div>
			<div class="controls">
			<?php if ($file_count > 0) { ?>
			<button disabled="disabled" class="button btn" id="djc_start_deleting">
				<?php echo JText::sprintf('COM_DJCATALOG2_IMAGES_DELETE_BUTTON', $file_count); ?>
			</button>
			<?php } else { ?>
			<button disabled="disabled" class="button btn"><?php echo JText::_('COM_DJCATALOG2_NOTHING_TO_DELETE'); ?></button>
			<?php } ?>
			</div>
		</div>
		</fieldset>
</div>
</div>