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

$tag_label = JText::_($params->get( 'tag_label','LOCATOR_TAG'));

	
		
?>
<div class="locator_form tags">
	<div class="inner">
		<span class="label"><?php echo $tag_label; ?></span>
		<?php 
		echo @$this->lists['tags']; 
		?>
	</div>
</div>