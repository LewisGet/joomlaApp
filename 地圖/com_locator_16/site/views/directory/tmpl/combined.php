<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
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


$doc =& JFactory::getDocument();
$this->initDOMLoadHook($params);
$doc->addScriptDeclaration("jqLocator(document).ready(initialize);");

$language = $params->get('gmap_language','en-GB');

if($params->get('use_ssl',0) == 1){
	$doc->addScript("https://maps-api-ssl.google.com/maps/api/js?sensor=false&language=$language");
}else{
	$doc->addScript("http://maps.google.com/maps/api/js?sensor=false&language=$language");
}

if($params->get('openmarkeron',0) == 1){

	$doc = JFactory::getDocument();
	$doc->addScriptDeclaration('
	
	jqLocator(document).ready(function(){
	
		jqLocator(".com_locator_entry").hover(
		  function () {
		  	var id = jqLocator(this).attr("id");
		  	var marker_id;
		  	if(id.length > 0){
		  	
		  		marker_id = id.replace("com_locator_entry_","");
		  		if(marker_id >= 0){
		  			openMarkerWindow(marker_id);
		  		}
		  		
		  	}
		    //$(this).append($("<span> ***</span>"));
		  }, 
		  function () {
		    
		  }
		);
		
	});

	');
	
}

//$doc->addScript("http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js");

$maxresults = 			(int)$params->get('maxresults',0);
$gmap_default_lat = 	$params->get( 'gmap_default_lat',41.397);
$gmap_default_lng = 	$params->get( 'gmap_default_lng',-96.644 );
$gmap_default_zoom = 	$params->get( 'gmap_default_zoom',4 );
$gmap_width = 			$params->get( 'gmap_width','80%');
$left_width = 			$params->get( 'left_width','20%');
$left_height = 			$params->get( 'left_height','500');

$gmap_height = 			$params->get( 'gmap_height','500');

$distance_unit_label = 	JText::_($params->get( 'distance_unit','LOCATOR_M'));
$defaultmapview = 		$params->get( 'defaultmapview','ROADMAP' );
$showemptymap = 		$params->get( 'showemptymap','0');
$showlegend = 		$params->get( 'showlegend','0');

$use_initial_map = false;

//use our initial map position
if(JRequest::getVar('task') != 'search_zip' && $params->get('autofind',0) == 0){

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

$html_output = '';

$results_style = 'style="width:' . $left_width . '; height:'.$left_height.'px"';

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
			
			echo $this->loadTemplate('map_item');
			$html_output .= $this->loadTemplate('item');
			
		}
	}
}

if($use_initial_map == false){
	showCenterOnResults($params);
}

showMarkerClusterer($params);


?>
//don't allow for grey margin zoom
google.maps.event.addListener(map, 'zoom_changed', function() {
    zoomLevel = map.getZoom();

    if (zoomLevel == 0) {
      map.setZoom(1);
    }      
  }); 

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
<form name="adminForm" action="<?php echo JRoute::_('index.php') ?>" method="get">
<?php
require('components' .DS . 'com_locator' . DS . 'helpers' .DS . 'directory' .DS . 'helper.php');

//display each directory entry
if(count($this->items) > 0 && $params->get('showsearchresultstring',1) == 1){
?>
<div class="found"><?php echo JText::_('LOCATOR_FOUND'); ?><span class="total">&nbsp;<?php echo (int)$this->total; ?></span> <?php echo JText::_('LOCATOR_LOCATIONS'); ?></div>
<?php
}
$hide_map = '';
if(count($this->items) > 0){
?>
<div class="com_locator_results_wrapper combined" <?php echo $results_style; ?> >
<?php

echo $html_output;

?>
</div><!-- end results wrapper -->
<?php 

}else{
	$this->showNoResults();
	
	if($showemptymap == 0){
		$hide_map = "display:none;";
	}
}

?>
<div id="locator_map_canvas" class="locator_combined_gmap" style="width:<?php echo $gmap_width; ?>;height:<?php echo $gmap_height; ?>px;<?php echo $hide_map; ?>"></div>
<?php 

if(count($this->items) > 0){
	$this->showPagination($params);
}

?>
<?php if($showlegend == 1){ ?>
<div id="locator_map_legend" class="locator_combined_legend" style="width:<?php echo $gmap_width; ?>;<?php echo $hide_map; ?>">
<?php 
if(count($this->lists['activetags']) > 0){
	
	?><ul class="locator_legend"><?php
	
	foreach ($this->lists['activetags'] as $t){
		
		$image = '';
		
		if(strlen($t->marker) <= 0){
			$t->marker = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png';
		}
		
		$image = '<span class="locator_icon"><img src="'.$t->marker.'" alt="'.$t->name.'" /></span>';	
	
		echo '<li>'.$image.'<span class="locator_icon_label">'.$t->name.'</span></li>';
		
	}
	
	?></ul><?php
}
?>
</div>
<?php }
if(count($this->items) > 0){
?>
<div id="locator_map_directions" class="locator_combined_directions"></div>
<?php } ?>
</form>
