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

class TableProductAttributes extends JTable
{
	
	/** @var int Primary key */
	var $productattribute_id = null;
	
	/** @var int */
	var $product_id = null;	
	
	/** @var int */
	var $productattribute_name = null;
	
	/** @var int */
	var $ordering = null;
		
	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct('#__j2store_productattributes', 'productattribute_id', $db );
	}
	
	
	function check()
	{
		if (empty($this->product_id))
		{
			$this->setError( JText::_( "Product Association Required" ) );
			return false;
		}
        if (empty($this->productattribute_name))
        {
            $this->setError( JText::_( "Attribute Name Required" ) );
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
        parent::reorder('product_id = '.$this->_db->Quote($this->product_id) );
    }
    
	
	 function delete( $oid=null )
    {
        if ($oid) 
        { 
            $k = $oid;
        } 
            else 
        { 
            $k = $this->_tbl_key;
        }
        
        if ($return = parent::delete( $oid ))
        {
            // also delete all PAOs for this PA
            $query = 'DELETE FROM #__j2store_productattributeoptions WHERE productattribute_id='.$k;
            $this->_db->setQuery( (string) $query );
            $this->_db->query();          
        }
        return $return;
    }

}
?>
