<?php
/**
 * @version $Id: default_items.php 114 2013-02-01 10:04:49Z michal $
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

<?php
$k = 0; 
$i = 1; 
$col_count = $this->params->get('items_columns',2);
$col_width = ((100/$col_count)-0.01);

foreach ($this->items as $item) {
	$newrow_open = $newrow_close = false;
	if ($k % $col_count == 0) $newrow_open = true;
	if (($k+1) % $col_count == 0 || count($this->items) <= $k+1) $newrow_close = true;
	        
	$rowClassName = 'djc_clearfix djc_item_row djc_item_row';
	if ($k == 0) $rowClassName .= '_first';
	if (count($this->items) <= ($k + $this->params->get('items_columns',2))) $rowClassName .= '_last';
	
	$colClassName ='djc_item_col';
	if ($k % $col_count == 0) { $colClassName .= '_first'; }
	else if (($k+1) % $col_count == 0) { $colClassName .= '_last'; }
	else {$colClassName .= '_'.($k % $col_count);}
	$k++;
	
	if ($newrow_open) { $i = 1 - $i; ?>
	<div class="<?php echo $rowClassName.'_'.$i; ?> djc2_cols_<?php echo $col_count ?>">
	<?php }
	?>
        <div class="djc_item pull_left <?php echo $colClassName; if ($item->featured == 1) echo ' featured_item'; ?>" style="width:<?php echo $col_width; ?>%">
        <div class="djc_item_bg">
		<div class="djc_item_in djc_clearfix">
		<?php if ($item->featured == 1) { 
			echo '<img class="djc_featured_image" alt="'.JText::_('COM_DJCATALOG2_FEATURED_ITEM').'" title="'.JText::_('COM_DJCATALOG2_FEATURED_ITEM').'" src="'.DJCatalog2ThemeHelper::getThemeImage('featured.png').'" />';
		}?>
        <?php if ($item->item_image) { ?>
        	<div class="djc_image">
        		<?php if ($this->params->get('image_link_item')) { ?>
					<a rel="lightbox-djitem" title="<?php echo $item->image_caption; ?>" href="<?php echo DJCatalog2ImageHelper::getImageUrl($item->item_image,'fullscreen'); ?>"><img class="img-polaroid" alt="<?php echo $item->image_caption; ?>" src="<?php echo DJCatalog2ImageHelper::getImageUrl($item->item_image,'medium'); ?>"/></a>
				<?php } else { ?>
					<a href="<?php echo JRoute::_(DJCatalogHelperRoute::getItemRoute($item->slug, (!isset($this->item) || (empty($this->item->catslug))) ? $item->catslug:$this->item->catslug)); ?>"><img class="img-polaroid" alt="<?php echo $item->image_caption; ?>" src="<?php echo DJCatalog2ImageHelper::getImageUrl($item->item_image,'medium'); ?>"/></a>
	        	<?php } ?>
        	</div>
		<?php } ?>
		<div class="djc_title">
	        <h3><?php
	          echo (JHTML::link(JRoute::_(DJCatalogHelperRoute::getItemRoute($item->slug, (!isset($this->item) || (empty($this->item->catslug))) ? $item->catslug:$this->item->catslug)), $item->name));
	        ?></h3>
	    </div>
            <div class="djc_description">
				<?php if ($this->params->get('show_category_name') > 0 && $item->publish_category) { ?>
				<div class="djc_category_info">
	            	<?php 
					if ($this->params->get('show_category_name') == 2) {
	            		echo JText::_('COM_DJCATALOG2_CATEGORY').': '?>
	            		<span><?php echo $item->category; ?></span> 
					<?php }
					else {
						echo JText::_('COM_DJCATALOG2_CATEGORY').': ';?>
						<a href="<?php echo JRoute::_(DJCatalogHelperRoute::getCategoryRoute($item->catslug));?>">
							<span><?php echo $item->category; ?></span>
						</a> 
					<?php } ?>
	            </div>
				<?php } ?>
				<?php if ($this->params->get('show_producer_name') > 0 && $item->producer && $item->publish_producer) { ?>
				<div class="djc_producer_info">
					<?php 
					if ($this->params->get('show_producer_name') == 2) {
	            		echo JText::_('COM_DJCATALOG2_PRODUCER').': '; ?>
	            		<span><?php echo $item->producer;?></span>
					<?php }
					else if(($this->params->get('show_producer_name') == 3)) {
						echo JText::_('COM_DJCATALOG2_PRODUCER').': ';?>
						<a class="modal" rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="<?php echo JRoute::_(DJCatalogHelperRoute::getProducerRoute($item->prodslug).'&tmpl=component'); ?>">
							<span><?php echo $item->producer; ?></span>
						</a> 
					<?php }
					else {
						echo JText::_('COM_DJCATALOG2_PRODUCER').': ';?>
						<a href="<?php echo JRoute::_(DJCatalogHelperRoute::getProducerRoute($item->prodslug)); ?>">
							<span><?php echo $item->producer; ?></span>
						</a> 
					<?php } ?>
					<?php if ($this->params->get('show_producers_items', 1)) { ?>
						<a class="djc_producer_items_link btn btn-mini button" href="<?php echo JRoute::_(DJCatalogHelperRoute::getCategoryRoute(0).'&cm=0&pid='.$item->prodslug); ?>"><span><?php echo JText::_('COM_DJCATALOG2_SHOW_PRODUCERS_ITEMS'); ?></span></a>
        			<?php } ?>
	            </div>
				<?php } ?>
	            <?php 
					if ($price_auth && ($this->params->get('show_price') == 2 || ( $this->params->get('show_price') == 1 && $item->price > 0.0))) { 
				?>
	            <div class="djc_price">
	            	<?php echo JText::_('COM_DJCATALOG2_PRICE').': ';?><span><?php echo DJCatalog2HtmlHelper::formatPrice($item->price, $this->params); ?></span>
	            </div>
				<?php } ?>
				
				<?php if ($this->params->get('items_show_intro')) { ?>
				<div class="djc_introtext">
					<?php if ($this->params->get('items_intro_length') > 0 ) {
							?><p><?php echo DJCatalog2HtmlHelper::trimText($item->intro_desc, $this->params->get('items_intro_length'));?></p><?php
						}
						else {
							echo $item->intro_desc; 
						}
					?>
				</div>
				<?php } ?>
				
				<?php if ($this->params->get('items_show_attributes', '1')) { ?>
					<?php 
					if (count($this->attributes) > 0) {
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
					<?php } ?>
				<?php } ?>
            </div>
            <?php if ($this->params->get('showreadmore_item')) { ?>
				<div class="clear"></div>
				<div class="djc_readon">
					<a class="btn button readmore" href="<?php echo JRoute::_(DJCatalogHelperRoute::getItemRoute($item->slug, (!isset($this->item) || (empty($this->item->catslug))) ? $item->catslug:$this->item->catslug)); ?>" class="readmore"><?php echo JText::sprintf('COM_DJCATALOG2_READMORE'); ?></a>
				</div>
			<?php } ?>
         </div>
 	</div>
	<div class="djc_clear"></div>
	</div>
	<?php if ($newrow_close) { ?>
		</div>
	<?php } ?>
<?php } ?>