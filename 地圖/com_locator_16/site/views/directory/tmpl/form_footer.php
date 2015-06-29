<?php

/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$component = JComponentHelper::getComponent( 'com_locator' );
$params = new JParameter( $component->params );

$menuitemid = JRequest::getInt( 'Itemid' );

if ($menuitemid)
{
	$menu = JSite::getMenu();
	$menuparams = $menu->getParams( $menuitemid );
	$params->merge( $menuparams );
}

$requirepostalcode = $params->get('requirepostalcode');

$Itemid = JRequest::getInt('Itemid');
$menu = JSite::getMenu();

$show_submit = false;
$item = $menu->getItem($Itemid);
$active = new JURI($item->link);

$active->setVar('Itemid', $Itemid);

//TODO: this sucks, we should find a better way to do this
if($params->get('showzipform')  || $params->get('showtagcheckboxes') || $params->get('showtaggroups') || $params->get('showcitydropdown') || $params->get('showstatedropdown') || $params->get('showtagdropdown') || $params->get('showkeywordform') || $params->get('showcountrydropdown')){
	$show_submit = true;
}else{
	//no form elements are displayed, we need to ensure whatever search made from the module continues here
}
	
?>
<input type="hidden" id="Itemid" value="<?php echo (int)$_REQUEST['Itemid']; ?>" name="Itemid" />
<input type="hidden" value="<?php echo (isset($_REQUEST['view']))?($_REQUEST['view']):(""); ?>" name="view" />
<input type="hidden" value="<?php echo (isset($_REQUEST['layout']))?($_REQUEST['layout']):("default"); ?>" name="layout" />

<?php if(strlen(JRequest::getWord('tmpl')) > 0){ ?>
<input type="hidden" value="<?php echo (isset($_REQUEST['tmpl']))?($_REQUEST['tmpl']):(""); ?>" name="tmpl" />
<?php } ?>

<?php if(JRequest::getInt('framed') == 1){ ?>
<input type="hidden" value="1" id="framed" name="framed" />
<?php } ?>

<input type="hidden" value="search_zip" name="task" />
<input type="hidden" value="com_locator" name="option" />
<?php

//mobile shows the submit button above the map
if($show_submit == true){
	
	if($params->get('showreset',0) == 1){
		?>
		<input class="locator_button locator_reset" type="reset" name="reset" value="Reset" onclick="window.location.href='<?php echo $link; ?>'"/>
		<?php
	}
	
	if(defined('LOCATOR_POSTAL_CODE')){ ?>
	<input class="locator_button locator_submit" type="button" value="<?php echo JText::_('LOCATOR_SUBMIT'); ?>" name="go" onclick="<?php if(defined('LOCATOR_MODULE')){ echo "module_"; }?>sendPostalQuery(document.getElementById('postal_code').value,document.adminForm);"/>
	<?php } else { ?>
	<input type="submit" value="<?php echo JText::_('LOCATOR_SUBMIT'); ?>" name="go" />
	<?php 
	}
} ?>