<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$ 
 */

defined('_JEXEC') or die('Restricted access');


function locatorBuildRoute( &$query )
{
	
       $segments = array();
       if(isset($query['view']))
       {
                $segments[] = $query['view'];
                unset( $query['view'] );
       }
       
       if(isset($query['id']))
       {
       			
       			//NOTE: This requires a database hit for every location displayed on the page and 
       			// may cause performance issues.
       			$sql = 'SELECT name from #__locations WHERE id='.$query['id'];
				$db =& JFactory::getDBO();
				$db->setQuery($sql);
				$name = $db->loadResult();
	
				$name = clean_string($name);
									
                $segments[] = $query['id'] . ':' . $name;
                
                unset( $query['id'] );
       }
       
       if(isset($query['layout'])){
       		$segments[] = $query['layout'];
       		unset($query['layout']);
       }
       
       return $segments;
}

function clean_string($name){
		
	$name = str_replace(array('.','/',"'"),'',$name);
	return $name;
	
}

function locatorParseRoute( $segments )
{
       $vars = array();

       $vars['view'] = $segments[0];
        
       switch($segments[0])
       {
               case 'directory':
                       $vars['view'] = 'directory';
                       break;
               case 'location':
               	
                       $vars['view'] = 'location';
                       $id = explode( ':', $segments[1] );
                       $vars['id'] = (int)$id[0];
                
                     
                       break;
       }
       
       if(isset($segments[1])){
	
	       switch($segments[1])
	       {
	               case 'combined':
	                       $vars['layout'] = 'combined';
	                       break;
	               case 'gmap':
	                       $vars['layout'] = 'gmap';
	                       break;
	               case 'search':
	                       $vars['layout'] = 'search';
	                       break;
					default:{
						 $vars['layout'] = 'default';
					}break;
	       }
       
       }
    
       return $vars;
}