<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id: gmap.php 637 2011-02-02 23:01:48Z fatica tc
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$doc =& JFactory::getDocument();


$params = &JComponentHelper::getParams( 'com_locator' );

$menuitemid = JRequest::getInt( 'Itemid' );
  
if ($menuitemid)
{
	$menu = JSite::getMenu();
	$menuparams = $menu->getParams( $menuitemid );
	$params->merge( $menuparams );
}

$language = $params->get('gmap_language','en-GB');

if($params->get('use_ssl',0) == 1){
	$doc->addScript("https://maps-api-ssl.google.com/maps/api/js?sensor=false&language=$language");
}else{
	$doc->addScript("http://maps.google.com/maps/api/js?sensor=false&language=$language");
}

$doc =& JFactory::getDocument();
$this->initDOMLoadHook($params);
$doc->addScriptDeclaration("jqLocator(document).ready(initialize);");

$maxresults = 			(int)$params->get('maxresults',0);
$gmap_default_lat = 	$params->get( 'gmap_default_lat',41.397);
$gmap_default_lng = 	$params->get( 'gmap_default_lng',-96.644 );
$gmap_default_zoom = 	$params->get( 'gmap_default_zoom',4 );
$gmap_width = 			$params->get( 'gmap_width','550');
$gmap_height = 			$params->get( 'gmap_height','500');
$distance_unit_label = 	JText::_($params->get( 'distance_unit','LOCATOR_M'));
$defaultmapview = 		$params->get( 'defaultmapview','ROADMAP' );
$showemptymap = 		$params->get( 'showemptymap','0');

$use_initial_map = false;

//use our initial map position
if(JRequest::getVar('task') != 'search_zip'){


	if(strlen($params->get( 'gmap_initial_lat')) > 0){
		$gmap_default_lat = 	$params->get( 'gmap_initial_lat');
		$use_initial_map = true;
	}
	
	if(strlen($params->get( 'gmap_initial_lng')) > 0){
		$gmap_default_lng = 	$params->get( 'gmap_initial_lng' );
		
	}
	
	if(strlen($params->get( 'gmap_initial_zoom')) > 0){
		$gmap_default_zoom = 	$params->get( 'gmap_initial_zoom' );
	}
	
}

ob_start();

$path = JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'directory' . DS . 'mapinit.php';
		
require_once($path);
			        
for ($i = 0; $i < count($this->items);  $i++){
	
	$item = $this->items[$i];

	$j = $i + 1;
	
	for ($i = 0; $i < count($this->items);  $i++){
		
		$item = $this->items[$i];

		$this->item =& $item;
		$this->params =&$params;
		$this->index = $i;
		
		if($item->id > 0 && strlen($item->lat) > 0){		
			$this->setLayout('combined');
			echo $this->loadTemplate('map_item');
			$this->setLayout('gmap');
		}
	}
}

if($use_initial_map == false){
	showCenterOnResults($params);
}

showMarkerClusterer($params);

?>

}  //end init function
<?php
$buffer = ob_get_contents();
ob_end_clean();
$doc->addScriptDeclaration($buffer);
?>
<!-- start com_locator  -->
<?php
if ( $params->get( 'show_page_title', 0 ) == 1 || $params->get( 'show_page_heading', 0 ) == 1 ){ ?>
<h1 class="com_locator_title">
	<?php echo (strlen($params->get('page_title')) > 0)?($params->get('page_title')):($params->get('page_heading')); ?>
</h1>
<?php } ?>

<form name="adminForm" action="<?php echo JRoute::_('index.php'); ?>" method="get">
<?php
require('components' .DS . 'com_locator' . DS . 'helpers' .DS . 'directory' .DS . 'helper.php');

//display each directory entry
if(count($this->items) && $params->get('showsearchresultstring',1) == 1){
?>
<div class="found"><?php echo JText::_('LOCATOR_FOUND'); ?><span class="total">&nbsp;<?php echo (int)$this->total; ?></span> <?php echo JText::_('LOCATOR_LOCATIONS'); ?></div>

<?php
}else{

	$this->showNoResults();
}

$hide_map = '';

if($showemptymap == 0 && count($this->items) == 0){
	$hide_map = "display:none;";
} 

?>
<div id="locator_map_canvas" class="locator_gmap" style="width:<?php echo $gmap_width; ?>px; height:<?php echo $gmap_height; ?>px; <?php echo $hide_map; ?>"></div>
<div id="locator_map_directions" class="locator_combined_directions"></div>
</form>