<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch ($task)
{
	case 'add':
	case 'new_content_typed':
	case 'new_content_section':
		TOOLBAR_locator::_EDIT(false);
		break;
	case 'edit':
	case 'editA':
	case 'tag':

	case 'edit_content_typed':
		TOOLBAR_locator::_EDIT(true);
		break;
	
		case 'import':
		TOOLBAR_locator::_IMPORT(true);
	break;
		case 'geocode':
		TOOLBAR_locator::_GEO(true);			
	break;
	case 'showsearch':
		TOOLBAR_locator::_SEARCH(true);			
	break;
	case 'addfield':
	case 'editfield':
		TOOLBAR_locator::_EDITF(true);
		break;
	case 'addtag':
	case 'edittag':		
		TOOLBAR_locator::_EDITM(true);
		break;
	
	case 'movesect':
		TOOLBAR_locator::_MOVE();
		break;

	case 'copy':
		TOOLBAR_locator::_COPY();
		break;
	case 'utils':
		TOOLBAR_locator::_UTILS();
		break;		
	case 'managetags':
		TOOLBAR_locator::_TAGS();
		break;
	case 'managefields':
		TOOLBAR_locator::_FIELDS();
		break;
	default:
		TOOLBAR_locator::_DEFAULT();
		break;
}