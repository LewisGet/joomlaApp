<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$lang =& JFactory::getLanguage();
$lang->load( 'com_locator', JPATH_SITE );

foreach ($this->lists['tag_groups_checkboxes'] as $tag_group){
?>
<div class="locator_form tag_groups">
	<div class="inner">
<?php

?>	<span class="tag_group">
	<span class="label"><?php echo JText::_(@$tag_group->name); ?></span>
	<?php 
		if(count($tag_group->checkboxes)){
		
			foreach ($tag_group->checkboxes as $cb){
				
				echo $cb;	
				
			}
				
		}

	?>
	</span>

	</div>
</div>
<?php
}
?>