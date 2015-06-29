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

?>
<div class="locator_form tag_groups">
	<div class="inner">
<?php
foreach ($this->lists['tag_groups'] as $tag_group){
?>	<span class="tag_group">
	<span class="label"><?php echo JText::_(@$tag_group->name); ?></span>
	<?php 
		echo @$tag_group->select; 
	?>
	</span>
<?php
}
?>
	</div>
</div>