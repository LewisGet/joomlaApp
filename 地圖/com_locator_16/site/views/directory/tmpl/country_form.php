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
<div class="locator_form country">
	<div class="inner">
		<span class="label"><?php echo JText::_('LOCATOR_COUNTRY'); ?></span>
		<?php 
			echo $this->lists['country']; 
		?>
	</div>
</div>