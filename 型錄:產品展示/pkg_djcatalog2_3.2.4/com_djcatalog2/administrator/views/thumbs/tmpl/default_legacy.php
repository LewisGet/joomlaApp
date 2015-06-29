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

defined('_JEXEC') or die('Restricted access'); 
JHtml::_('behavior.tooltip');
?>


<div class="width-100">
<fieldset class="adminform">
	
	<ul class="adminformlist">
	<li>
		<label for="djc_start_recreation" class="hasTip" title="<?php echo JText::_('COM_DJCATALOG2_THUMBNAILS_RECREATOR_LABEL_DESC'); ?>"><?php echo JText::_('COM_DJCATALOG2_RECREATE_THUMBNAILS'); ?></label>
		<button disabled="disabled" class="button" id="djc_start_recreation">
			<?php echo JText::_('COM_DJCATALOG2_THUMBNAILS_RECREATOR_BUTTON'); ?>
		</button>
	</li>
	<li>
		<span class="faux-label">&nbsp;</span>
		<div class="djc_thumbrecreator">
			<div style="clear: both" class="clr"></div>
			<div id="djc_progress_bar_outer">
				<div id="djc_progress_bar"></div>
				<div style="clear: both" class="clr"></div>
				<div id="djc_progress_percent">
					0%
				</div>
			</div>
		</div>
	<li>
	<?php 
		$files = JFolder::files(DJCATIMGFOLDER.DS.'custom', '.', false, false, array('index.html', '.svn', 'CVS', '.DS_Store', '__MACOSX')); 
		$file_count = count($files);
		?>
		
		<li>
			<label for="djc_start_deleting" class="hasTip" title="<?php echo JText::_('COM_DJCATALOG2_IMAGES_DELETE_LABEL_DESC'); ?>"><?php echo JText::_('COM_DJCATALOG2_IMAGES_DELETE_LABEL'); ?></label>
			<?php if ($file_count > 0) { ?>
			<button disabled="disabled" class="button btn" id="djc_start_deleting">
				<?php echo JText::sprintf('COM_DJCATALOG2_IMAGES_DELETE_BUTTON', $file_count); ?>
			</button>
			<?php } else { ?>
			<button disabled="disabled" class="button btn"><?php echo JText::_('COM_DJCATALOG2_NOTHING_TO_DELETE'); ?></button>
			<?php } ?>
		</li>
	</ul>
</fieldset>
<div style="clear: both" class="clr"></div>
</div>
