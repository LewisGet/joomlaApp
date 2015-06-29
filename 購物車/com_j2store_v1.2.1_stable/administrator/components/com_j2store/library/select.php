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


/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'html'.DS.'select.php' );
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'prices.php');
class J2StoreSelect extends JHTMLSelect
{   
	 /**
	 * Generates a +/- select list for pao prefixes
	 * 
	 * @param unknown_type $selected
	 * @param unknown_type $name
	 * @param unknown_type $attribs
	 * @param unknown_type $idtag
	 * @param unknown_type $allowAny
	 * @param unknown_type $title
	 * @return unknown_type
	 */
    public static function productattributeoptionprefix( $selected, $name = 'filter_prefix', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title = 'Select Prefix' )
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( $title )." -" );
        }

        $list[] = JHTML::_('select.option',  '+', "+" );
        $list[] = JHTML::_('select.option',  '-', "-" );
        $list[] = JHTML::_('select.option',  '=', "=" );

        return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
    }
    
    /**
	 * Generates a selectlist for the specified Product Attribute 
	 *
	 * @param unknown_type $productattribute_id 
	 * @param unknown_type $selected
	 * @param unknown_type $name
	 * @param unknown_type $attribs
	 * @param unknown_type $idtag
	 * @return unknown_type
	 */
    
     public static function productattributeoptions( $productattribute_id, $selected, $name = 'filter_pao', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $opt_selected = array())
    {
        $list = array();
        
        JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'models' );
        $model = JModel::getInstance( 'ProductAttributeOptions', 'J2StoreModel' );
        $model->setId($productattribute_id );
        $model->setState('order', 'a.ordering');
        $items = $model->getAllData();
        foreach (@$items as $item)
        {
        	if($item->productattributeoption_prefix != '=')
        	{
        		$display_suffix = ($item->productattributeoption_price > '0') ? ": ".$item->productattributeoption_prefix.J2StorePrices::number($item->productattributeoption_price) : '';
        	}
        	else
        	{
        		$display_suffix = ($item->productattributeoption_price > '0') ? ": ".J2StorePrices::number($item->productattributeoption_price) : '';
        	}
        	$display_name = JText::_($item->productattributeoption_name).$display_suffix;
            $list[] =  self::option( $item->productattributeoption_id, $display_name );
        }
        return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag  );
    }	    
	
}
