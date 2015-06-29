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

<?php if(isset($this->lists['states'])){ ?>

<div class="locator_form state">
	<div class="inner">
		<span class="label"><?php echo JText::_('LOCATOR_STATE'); ?></span>
		<?php 
		echo $this->lists['states']; 
		?>
	</div>
</div>

<?php 

} ?>