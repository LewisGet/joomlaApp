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

$app = JFactory::getApplication();

//check that the template was not loaded
if(JRequest::getString('tmpl','') != 'component'){

	$active = $menu->getActive();
	$url = $active->link . '&Itemid=' . $menuitemid . '&tmpl=component&no_html=1';
	$app->redirect($url);
	
}


$this->initDOMLoadHook($params);
$doc->addScriptDeclaration("jqLocator(document).ready(initialize);");

$maxresults = 			(int)$params->get('maxresults',0);
$gmap_default_lat = 	$params->get( 'gmap_default_lat',41.397);
$gmap_default_lng = 	$params->get( 'gmap_default_lng',-96.644 );
$gmap_default_zoom = 	$params->get( 'gmap_default_zoom',4 );
$gmap_height = 			$params->get( 'gmap_height','250');
$distance_unit_label = 	JText::_($params->get( 'distance_unit','LOCATOR_M'));
$defaultmapview = 		$params->get( 'defaultmapview','ROADMAP' );
$showemptymap = 		$params->get( 'showemptymap','0');
$hide_map = '';
$html_output = '';


$language = $params->get('gmap_language','en-GB');

if($params->get('use_ssl',0) == 1){
	$doc->addScript("https://maps-api-ssl.google.com/maps/api/js?sensor=false&language=$language");
}else{
	$doc->addScript("http://maps.google.com/maps/api/js?sensor=false&language=$language");
}

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
			$html_output .= $this->loadTemplate('item');
			$this->setLayout('mobile');
			
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
<html>
<head>
<title></title>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<link rel="stylesheet" type="text/css" href="components/com_locator/assets/locator_mobile.css" />
<?php
//media="only screen and (max-width: 480px)" 
foreach ($doc->_scripts as $script=>$type){

	
	if(is_array($type)){
		$type = $type['mime'];
	}
	
	echo '<script type="' . $type . '" src="' . $script . '"></script>
	';
}

foreach ($doc->_script as $type=>$script){
	if(is_array($type)){
		$type = $type['mime'];
	}
	echo '<script type="' . $type . '">'.$script.'</script>
	';
}
?>
</head>
<body>
<div class="locator_toolbar">
	<!-- start com_locator  -->
	<form name="adminForm" action="<?php echo JRoute::_('index.php') ?>" method="get">
	<?php
	require('components' .DS . 'com_locator' . DS . 'helpers' .DS . 'directory' .DS . 'helper.php');
	
	if(count($this->items) == 0){
		
		if(JRequest::getVar('task') == "search_zip"){
	?>
		<h4><?php echo JText::_('LOCATOR_NO_RESULTS'); ?></h4>
	<?php
		}
	}
	?>
</div>
<div id="locator_map_canvas" class="locator_combined_gmap" style="margin:0px; padding:0px; width:100%; height:70%; <?php echo $hide_map; ?>"></div>

<div id="locator_map_directions" class="locator_combined_directions"></div>

</form>
</body>
</html>
