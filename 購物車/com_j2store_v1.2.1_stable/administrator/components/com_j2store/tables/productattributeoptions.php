<?php
/*------------------------------------------------------------------------
# com_j2store - J2 Store v 1.0
# ------------------------------------------------------------------------
# author    Sasi varna kumar - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2012 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://j2store.org
# Technical Support:  Forum - http://j2store.org/forum/index.html
-------------------------------------------------------------------------*/


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class TableProductAttributeOptions extends JTable
{
	
	/** @var int Primary key */
	var $productattributeoption_id = null;
	
	/** @var int */
	var $productattribute_id = null;	
	
	/** @var string */
	var $productattributeoption_name= null;
	
	/** @var string */
	var $productattributeoption_price= null;
	
	/** @var string */
	var $productattributeoption_code= null;
	
	/** @var string */
	var $productattributeoption_prefix= null;
	
	/** @var int */
	var $ordering = null;
		
	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct('#__j2store_productattributeoptions', 'productattributeoption_id', $db );
	}
	
	
	function check()
	{
		if (empty($this->productattribute_id))
		{
			$this->setError( JText::_( "Product Attribute Association Required" ) );
			return false;
		}
        if (empty($this->productattributeoption_name))
        {
            $this->setError( JText::_( "Attribute Option Name Required" ) );
            return false;
        }
		return true;
	}
	
	
	function save()
	{
	    $this->_isNew = false;
	    $key = $this->getKeyName();
	    if (empty($this->$key))
        {
            $this->_isNew = true;
        }
        
		if ( !$this->check() )
		{
			return false;
		}
		
		if ( !$this->store() )
		{
			return false;
		}
		
		if ( !$this->checkin() )
		{
			$this->setError( $this->_db->stderr() );
			return false;
		}
		
		$this->reorder();
		
		
		$this->setError('');
		
		// TODO Move ALL onAfterSave plugin events here as opposed to in the controllers, duh
        //$dispatcher = JDispatcher::getInstance();
        //$dispatcher->trigger( 'onAfterSave'.$this->get('_suffix'), array( $this ) );
		return true;
	}
	
	
	 function reorder()
    {
        parent::reorder('productattribute_id = '.$this->_db->Quote($this->productattribute_id) );
    }
    

}
?>
