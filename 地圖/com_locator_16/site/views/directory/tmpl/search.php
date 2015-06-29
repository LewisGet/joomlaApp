<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: search.php 983 2012-01-15 10:19:44Z fatica $
 * tc
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$params = &JComponentHelper::getParams( 'com_locator' );

$menuitemid = JRequest::getInt( 'Itemid' );

if ($menuitemid)
{
	$menu = JSite::getMenu();
	$menuparams = $menu->getParams( $menuitemid );
	$params->merge( $menuparams );
}

$distance_unit_label = JText::_($params->get( 'distance_unit','LOCATOR_M'));

$doc =& JFactory::getDocument();

$doc =& JFactory::getDocument();
$this->initDOMLoadHook($params);


$linktoitempage = $params->get( 'linktoitempage',1 );
?>
<!-- start com_locator  -->
<?php
if ( $params->get( 'show_page_title', 0 ) == 1 || $params->get( 'show_page_heading', 0 ) == 1 ){ ?>
<h1 class="com_locator_title">
	<?php echo (strlen($params->get('page_title')) > 0)?($params->get('page_title')):($params->get('page_heading')); ?>
</h1>
<?php } ?>
<form name="adminForm" action="<?php echo JRoute::_('index.php') ?>" method="get">
<?php
require('components' .DS . 'com_locator' . DS . 'helpers' .DS . 'directory' .DS . 'helper.php');
?>
<div class="locator_results_wrapper">
<?php

//display each directory entry
if(count($this->items) && $params->get('showsearchresultstring',1) == 1){
	
	?>
	<div class="found"><?php echo JText::_('LOCATOR_FOUND'); ?><span class="total">&nbsp;<?php echo (int)$this->total; ?></span> <?php echo JText::_('LOCATOR_LOCATIONS'); ?></div>
<?php

	for ($i = 0; $i < count($this->items);  $i++){
		
		$item = $this->items[$i];

		$this->item =& $item;
		$this->params =&$params;
		$this->index = $i;
		
		if($item->id > 0){		
			$this->setLayout('combined');
			echo $this->loadTemplate('item');
			$this->setLayout('search');
			
		}
	}	
}else{
	
	$this->showNoResults();
}
?>
</div><!-- end results wrapper -->
<div style="clear:both;"></div>
<div class="locator_pagination">
<?php echo $this->pagination->getListFooter(); ?>

</div>
</form>