<?php
/**
 * @version $Id: default_filters.php 99 2013-01-08 10:39:32Z michal $
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
defined ('_JEXEC') or die('Restricted access');
?>
<div class="djc_filters_in thumbnail">
	<form name="djcatalogForm" id="djcatalogForm" method="post" action="index.php">
		<?php if ($this->params->get('show_category_filter') > 0 || $this->params->get('show_producer_filter') > 0) { ?>
			<ul class="djc_filter_list djc_clearfix">
				<li><span><?php echo JText::_('COM_DJCATALOG2_FILTER'); ?></span></li>
				<?php if ($this->params->get('show_category_filter') > 0) { ?>
					<li><?php echo $this->lists['categories'];?>
					<script type="text/javascript">
					//<![CDATA[ 
					document.id('cid').addEvent('change',function(evt){
						if(document.id('pid')) {
							options = document.id('pid').getElements('option');
							options.each(function(option, index){
								if (option.value == "") {
									option.setAttribute('selected', 'true');
								} else {
									option.removeAttribute('selected');
								}
							});
						}

						document.djcatalogForm.submit();
					});
					//]]>
					</script>
					</li>
				<?php } ?>
				<?php if ($this->params->get('show_producer_filter') > 0) { ?>
					<li><?php echo $this->lists['producers'];?></li>
					<script type="text/javascript">
						//<![CDATA[ 
						document.id('pid').addEvent('change',function(evt){
							document.djcatalogForm.submit();
						});
						//]]>
					</script>
				<?php } ?>
			</ul>
			<div class="clear"></div>
		<?php } ?>
		<?php if ($this->params->get('show_search') > 0) { ?>
			<ul class="djc_filter_search djc_clearfix">
				<li><span><?php echo JText::_('COM_DJCATALOG2_SEARCH'); ?></span></li>
				<li><input type="text" class="inputbox" name="search" id="djcatsearch" value="<?php echo $this->lists['search'];?>" /></li>
				<li><input type="submit" class="button btn" onclick="document.djcatalogForm.submit();" value="<?php echo JText::_( 'COM_DJCATALOG2_GO' ); ?>" /></li>
				<li><input type="submit" class="button btn" onclick="document.getElementById('djcatsearch').value='';document.djcatalogForm.submit();" value="<?php echo JText::_( 'COM_DJCATALOG2_RESET' ); ?>" /></li>
			</ul>
		<?php } ?>
	<?php if (!($this->params->get('show_category_filter') > 0)) { ?>
		<input type="hidden" name="cid" value="<?php echo JRequest::getVar('cid'); ?>" />
	<?php } ?>
	<?php if (!($this->params->get('show_producer_filter') > 0)) { ?>
		<input type="hidden" name="pid" value="<?php echo JRequest::getVar('pid'); ?>" />
	<?php } ?>
	<input type="hidden" name="option" value="com_djcatalog2" />
	<input type="hidden" name="view" value="items" />
	<input type="hidden" name="limitstart" value="0" />
	<input type="hidden" name="order" value="<?php echo JRequest::getVar('order','i.ordering'); ?>" />
	<input type="hidden" name="dir" value="<?php echo (JRequest::getVar('dir','asc')); ?>" />
	<input type="hidden" name="task" value="search" />
	<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>" />
	</form>
</div>