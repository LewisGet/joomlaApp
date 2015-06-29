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
<div class="locator_form keyword">
	<div class="inner">
		<span class="label"><?php echo JText::_('LOCATOR_KEYWORD'); ?></span><input type="text" name="keyword" id="keyword" class="inputbox" value="<?php echo JRequest::getString('keyword');?>"/>
	</div>
</div>