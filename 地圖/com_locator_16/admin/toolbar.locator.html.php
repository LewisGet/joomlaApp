<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */
/**
* @package		Joomla
* @subpackage	Content
*/
class TOOLBAR_locator
{
	function _SEARCH($edit)
	{
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$cid = intval($cid[0]);

		JToolBarHelper::title( JText::_( 'Search History' ), 'addedit.png' );
		
		JToolBarHelper::back(JText::_( 'Cancel' ));
		
	}
	
	function _EDIT($edit)
	{
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$cid = intval($cid[0]);

		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );

		JToolBarHelper::title( JText::_( 'Location' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		
		JToolBarHelper::save();
		
		JToolBarHelper::back(JText::_( 'Cancel' ));
		
	}
	function _EDITM($edit)
	{
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$cid = intval($cid[0]);

		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );

		JToolBarHelper::title( JText::_( 'Tag' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		
		JToolBarHelper::custom('savetag','save.png','save_f2.png',JText::_( 'Save' ),false,false);
		
		JToolBarHelper::back(JText::_( 'Cancel' ));
		
	}	
	function _EDITF($edit)
	{
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$cid = intval($cid[0]);

		$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );

		JToolBarHelper::title( JText::_( 'Fields' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
		
		JToolBarHelper::custom('savefield','save.png','save_f2.png',JText::_( 'Save' ),false,false);
		
		JToolBarHelper::back(JText::_( 'Cancel' ));
	}	
		
	function _GEO($edit)
	{


		JToolBarHelper::title( JText::_( 'Geocode' ), 'addedit.png' );
		
		JToolBarHelper::back(JText::_( 'Back' ));
		
	}	
	
	function _IMPORT($edit)
	{


		JToolBarHelper::title( JText::_( 'Import' ), 'addedit.png' );
		JToolBarHelper::custom('import_upload','save.png','save_f2.png',JText::_( 'Save' ),false,false);
		JToolBarHelper::back(JText::_( 'Back' ));
		
	}		
	
	function _UTILS(){
		

		
	}
	
	
	function _TAGS()
	{
		global $filter_state;

		JToolBarHelper::title( JText::_( 'Tag Manager' ), 'addedit.png' );
		JToolBarHelper::addNewX('addtag');
		JToolBarHelper::trash('removetags');
		JToolBarHelper::custom('admin','back.png','back_f2.png',JText::_( 'Back' ),false,false);

	}
	
	function _FIELDS()
	{
		global $filter_state;

		JToolBarHelper::title( JText::_( 'Field Manager' ), 'addedit.png' );
		JToolBarHelper::addNewX('addfield');
		JToolBarHelper::trash('removefields');
		JToolBarHelper::custom('admin','back.png','back_f2.png',JText::_( 'Back' ),false,false);

	}
		
	function _DEFAULT()
	{
		global $filter_state;

		JToolBarHelper::title( JText::_( 'Location Manager' ), 'addedit.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		
		JToolBarHelper::custom( 'managefields', 'copy.png', 'copy_f2.png',JText::_( 'Field Manager' ),false );		
		
		JToolBarHelper::custom( 'managetags', 'copy.png', 'copy_f2.png',JText::_( 'Tag Manager' ),false );
		JToolBarHelper::custom( 'tag', 'default.png', 'default_f2.png', JText::_( 'Tag' ),true );
		
		JToolBarHelper::custom( 'import', 'default.png', 'default_f2.png', JText::_( 'Import' ),false );
		JToolBarHelper::custom( 'export', 'default.png', 'default_f2.png', JText::_( 'Export All' ),false );

		JToolBarHelper::custom( 'geocode', 'default.png', 'default_f2.png', JText::_( 'Geocode' ),false );
		
		JToolBarHelper::trash();
		//JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		JToolBarHelper::preferences('com_locator', '550');

	}
}