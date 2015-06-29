<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
$distance_unit_label = JText::_($this->params->get( 'distance_unit','LOCATOR_M'));
$item =&$this->item;	
$i = $this->index;
$j = $i + 1;

$columns = $this->params->get( 'locator_columns', 1 );


$float_class 	= '';
$link 			= '';

if($columns == 2){
	$float_class = " locator_columns";	
}
?>
<div class="com_locator_entry row<?php echo $i % 2; ?> <?php echo $float_class; ?>" id="com_locator_entry_<?php echo $i;?>">
<?php
	
	$format = $this->formatItem($item,$this->params,'address_template');
	
	if($this->params->get('triggercontentplugin',0) == 1){
		$format = JHTML::_('content.prepare', $format);
	}

	//output the item template 
	?>
	<div class="com_locator_address">
		<?php
			echo $format;	
		?>		
	</div>
</div>	