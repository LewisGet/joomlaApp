<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$config =& JFactory::getConfig();

$params = &JComponentHelper::getParams( 'com_locator' );

$mainframe = JFactory::getApplication();
$mainframe->enqueueMessage("Multiple Locations selected");


$item = $this->items[0];
?>

<script language="javascript" type="text/javascript">

submitbutton = function (pressbutton) {
	
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	try {
		form.onsubmit();
	} catch(e) {
		//alert(e);
	}

	form.submit();


}

if(typeof(Joomla) != 'undefined'){
	Joomla.submitbutton = submitbutton;	
}
//-->
</script>

<form action="index.php" method="post" name="adminForm">
<fieldset>
<legend><?php echo JText::_('Tag Multiple Locations'); ?></legend>
<table class="adminform" width="100%">
<tr>
	<td width="10%">
	<?php echo JText::_('Tags'); ?>
	</td>
	<td>
	
		<?php

		if(!strlen($this->lists['tags'])){
		
			?>
			
			<h2>No tags created yet!</h2>
			<a href="index.php?option=com_locator&view=tags&Itemid=<?php echo JRequest::getInt('Itemid'); ?>"><?php echo JText::_('Create Tags'); ?></a>
			<?php
			
		}
		
		echo $this->lists['tags'];?>
	</td>
</tr>
</table>
</fieldset>

<input type="hidden" name="option" value="com_locator" />
<?php $ids = JRequest::getVar('cid'); 

foreach ($ids as $id){
?>
<input type="hidden" name="cid[]" value="<?php echo $id ?>" />
<?php }
?>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="task" value="savemultiple" />
<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid',''); ?>"/>
</form>
<?php echo JHTML::_('behavior.keepalive'); ?>