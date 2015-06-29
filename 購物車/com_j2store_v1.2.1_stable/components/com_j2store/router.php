<?php

// no direct access
defined('_JEXEC') or die('Restricted access');


function J2StoreBuildRoute( & $query) {

 $segments = array ();
 
 	$app		= JFactory::getApplication();
//	$menu		= $app->getMenu();
    $menu = & JSite::getMenu();
    if ( empty($query['Itemid'])) {
        $menuItem = & $menu->getActive();
    }
    else {
        $menuItem = & $menu->getItem($query['Itemid']);
    }
    
    $mView = ( empty($menuItem->query['view']))?null:$menuItem->query['view']; 
    $mTask = ( empty($menuItem->query['task']))?null:$menuItem->query['task'];
    $mId = ( empty($menuItem->query['id']))?null:$menuItem->query['id'];
    
	 if ( isset ($query['layout'])) {
        unset ($query['layout']);
    }
    
 if ( $mView == @$query['view'] && $mTask == @$query['task'] && $mId == @intval($query['id']) &&  @intval($query['id']) > 0 ) {
        unset ($query['view']);
        unset ($query['task']);
        unset ($query['id']);
    }
    
    
  if ( isset ($query['view'])) {
        $view = $query['view'];
        $segments[] = $view;
        unset ($query['view']);
    }

    if (@ isset ($query['task'])) {
        $task = $query['task'];
        $segments[] = $task;
        unset ($query['task']);
    }

    if ( isset ($query['id'])) {
        $id = $query['id'];
        $segments[] = $id;
        unset ($query['id']);
    }

    if ( isset ($query['cid'])) {
        $cid = $query['cid'];
        $segments[] = $cid;
        unset ($query['cid']);
    }
  
    
    return $segments;
    
}


function J2StoreParseRoute($segments) {
	
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$db = JFactory::getDBO();
	// Count route segments
	//print_r($segments);
	
    $vars = array ();
    $vars['view'] = $segments[0];
    if (!isset($segments[1]))
        $segments[1]='';
  	$vars['task'] = $segments[1];
  
 	if ($segments[0] == 'orders') {
		 if (isset($segments[1]) && ($segments[1] == 'view' || $segments[1] == 'printOrder' )) {
		 		$vars['id'] = $segments[2];
    	}	
    }
    
   return $vars;
  
}