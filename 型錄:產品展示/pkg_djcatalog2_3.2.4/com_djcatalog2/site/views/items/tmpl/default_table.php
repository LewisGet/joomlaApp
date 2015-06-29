<?php
/**
 * @version $Id: default_table.php 114 2013-02-01 10:04:49Z michal $
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
$user		= JFactory::getUser();
$price_auth = ($this->params->get('price_restrict', '0') == '1' && $user->guest) ? false : true;
?>
<table width="100%" cellpadding="0" cellspacing="0" class="djc_items_table jlist-table category" id="djc_items_table">
	<thead>
		<tr>
			<th class="djc_thead djc_th_image">&nbsp;</th>
			<th class="djc_thead djc_th_title">
				<?php echo JText::_('COM_DJCATALOG2_NAME'); ?>
	        </th>
			<?php if ($this->params->get('items_show_intro')) {?>
	                <th class="djc_thead djc_th_intro">
	                    <?php echo JText::_('COM_DJCATALOG2_DESCRIPTION'); ?>
	                </th>
			<?php } ?>
			<?php if ($this->params->get('show_category_name') > 0) { ?>
				<th class="djc_thead djc_th_category">
					<?php echo JText::_('COM_DJCATALOG2_CATEGORY'); ?>
				</th>
			<?php } ?>
			<?php if ($this->params->get('show_producer_name') > 0) { ?>
				<th class="djc_thead djc_th_producer">
					<?php echo JText::_('COM_DJCATALOG2_PRODUCER'); ?>
				</th>
			<?php } ?>
			<?php if ($price_auth && $this->params->get('show_price') > 0) { ?>
	                <th class="djc_thead djc_th_price">
	                	<?php echo JText::_('COM_DJCATALOG2_PRICE'); ?>
	                </th>
			<?php } ?>
			<?php if ($this->params->get('items_show_attributes', '1')) { ?>
				<?php if (count($this->attributes)) { ?>
		            <th class="djc_thead djc_th_price">
	                	<?php echo JText::_('COM_DJCATALOG2_CUSTOM_ATTRIBUTES'); ?>
	                </th>
	            <?php } ?>
			<?php } ?>
	            </tr>
            </thead>
            <tbody>
        <?php
	$k = 1;
	foreach($this->items as $item){
		$k = 1 - $k;
		?>
        <tr class="cat-list-row<?php echo $k;?> djc_row<?php echo $k; if ($item->featured == 1) echo ' featured_item'; ?>">
            <td class="djc_image">
                <?php if ($item->item_image) { ?>
	        	<div class="djc_image_in">
	        		<?php if ($this->params->get('image_link_item')) { ?>
						<a rel="lightbox-djitem" title="<?php echo $item->image_caption; ?>" href="<?php echo DJCatalog2ImageHelper::getImageUrl($item->item_image,'fullscreen'); ?>"><img class="img-polaroid" alt="<?php echo $item->image_caption; ?>" src="<?php echo DJCatalog2ImageHelper::getImageUrl($item->item_image,'small'); ?>"/></a>
					<?php } else { ?>
						<a href="<?php echo JRoute::_(DJCatalogHelperRoute::getItemRoute($item->slug, (!isset($this->item) || (empty($this->item->catslug))) ? $item->catslug:$this->item->catslug)); ?>"><img class="img-polaroid" alt="<?php echo $item->image_caption; ?>" src="<?php echo DJCatalog2ImageHelper::getImageUrl($item->item_image,'small'); ?>"/></a>
		        	<?php } ?>
	        	</div>
			<?php } ?>
            </td>
			<td class="djc_td_title" nowrap="nowrap">
                <?php  echo (JHTML::link(JRoute::_(DJCatalogHelperRoute::getItemRoute($item->slug, (!isset($this->item) || (empty($this->item->catslug))) ? $item->catslug:$this->item->catslug)), $item->name)); ?>
           		<?php if ($item->featured == 1) { 
					echo '<img class="djc_featured_image" alt="'.JText::_('COM_DJCATALOG2_FEATURED_ITEM').'" title="'.JText::_('COM_DJCATALOG2_FEATURED_ITEM').'" src="'.DJCatalog2ThemeHelper::getThemeImage('featured.png').'" />';
				}?>
            </td>
		<?php if ($this->params->get('items_show_intro')) {?>
		<td class="djc_introtext">
			<?php if ($this->params->get('items_intro_length') > 0 ) {
					echo DJCatalog2HtmlHelper::trimText($item->intro_desc, $this->params->get('items_intro_length'));
				}
				else {
					echo $item->intro_desc; 
				}
			?>
		 </td>
		<?php } ?>
		<?php if ($this->params->get('show_category_name') > 0 && $item->publish_category) { ?>
				<td class="djc_category" nowrap="nowrap">
					<?php 
						if ($this->params->get('show_category_name') == 2) {
	            			?><span><?php echo $item->category; ?></span> 
						<?php }
						else {
							?><a href="<?php echo JRoute::_(DJCatalogHelperRoute::getCategoryRoute($item->catslug)) ;?>"><span class="djcat_category"><?php echo $item->category; ?></span></a> 
						<?php } ?>
				</td>
			<?php } ?>
			<?php if ($this->params->get('show_producer_name') > 0) { ?>
				<td class="djc_producer" nowrap="nowrap">
				<?php if ($item->publish_producer && $item->producer) { ?>
					<?php 
						if ($this->params->get('show_producer_name') == 2 && $item->producer) {
	            			?><span><?php echo $item->producer;?></span>
						<?php }
						else if(($this->params->get('show_producer_name') == 3 && $item->producer)) {
							?><a class="modal" rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="<?php echo JRoute::_(DJCatalogHelperRoute::getProducerRoute($item->prodslug).'&tmpl=component'); ?>"><span class="djcat_producer"><?php echo $item->producer; ?></span></a> 
						<?php }
						else if ($item->producer){
							?><a href="<?php echo JRoute::_(DJCatalogHelperRoute::getProducerRoute($item->prodslug)); ?>"><span class="djcat_producer"><?php echo $item->producer; ?></span></a>
						<?php } ?>
					<?php } ?>
				</td>
			<?php } ?>
		<?php if ($price_auth && $this->params->get('show_price') > 0) { ?>
            <td class="djc_price" nowrap="nowrap">
                <?php
					if ($item->price > 0.0) {
						?><span><?php echo DJCatalog2HtmlHelper::formatPrice($item->price, $this->params); ?></span>
				<?php } ?>
            </td>
		<?php } ?>
		<?php if ($this->params->get('items_show_attributes', '1')) { ?>
			<?php 
			if (count($this->attributes) > 0) { ?>
				<td class="djc_attributes">
				<?php
				$attributes_body = '';
				foreach ($this->attributes as $attribute) {
						$this->attribute_cursor = $attribute;
						$this->item_cursor = $item;
						$attributes_body .= $this->loadTemplate('items_attributes');
					}
				?>
				<?php if ($attributes_body != '') { ?>
					<div class="djc_attributes">
						<table>
						<?php echo $attributes_body; ?>
						</table>
					</div>
					<?php } ?>
				</td>
				<?php } ?>
		<?php } ?>
        </tr>
	<?php } ?>
	</tbody>
</table>