<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.utilities.date');

/**
 * Content Component Article Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class EasyBlogModelUserApps extends JModel
{
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	function getUserApps($appName = '')	
	{
		$db	= JFactory::getDBO();
		$query	= 'SELECT * FROM `#__easyblog_apps`';
		if(! empty($appName))
			$query	.= ' WHERE `appname` = ' . $db->Quote($appName);
			
		$db->setQuery($query);
		
		$result	= $db->loadObjectList();
		return $result;			
	
	}
	
	
	/**
	 * Get user apps params.
	 * return null if user apps not found. return an object if found.	 
	 */	 	
	
	function getUserAppsParams($appsId, $userId, $params=null)
	{
		$db	= JFactory::getDBO();
		
		$query	= 'SELECT b.*'
				. 'FROM ' 	. $db->nameQuote('#__easyblog_userapps') . ' AS a, ' 
							. $db->nameQuote('#__easyblog_params') . ' AS b '
				. 'WHERE ' 	. 'a.' . $db->nameQuote('id') . ' = ' . 'b.' . $db->nameQuote('refer_id') . ' AND '
							. 'a.' . $db->nameQuote('app_id') . ' = ' . $db->quote($appsId) . ' AND '
							. 'a.' . $db->nameQuote('user_id') . ' = ' . $db->quote($userId) . ' AND '
							. 'b.' . $db->nameQuote('param_type') . ' = ' .	$db->quote('userapp');
							
		$db->setQuery($query);
		
		$result	= $db->loadObjectList();
				
		$obj	= null;
		
		if(! empty($result))
		{
			$obj	= new StdClass();												
			foreach($result as $row)
			{
				$key	= $row->param_name;
		
				$objAttr	= new StdClass();		
				$objAttr->id		= $row->id;
				$objAttr->referId	= $row->refer_id;				
				$objAttr->datatype	= $row->param_value_type;
				$objAttr->type		= $row->param_type;
				$objAttr->published	= $row->published;
				$objAttr->ordering	= $row->ordering;
				$objAttr->value		= $row->param_value;				
				
				$obj->$key		= $objAttr;								
								
			}
		}
		
		return $obj;
	}
	
		
	
	function saveUserAppsParams($mode = '', $userId, $appId, $referId = 0, $param = null)
	{
		$db		= JFactory::getDBO();
		$result	= true;
	
		if(! empty($mode))
		{
			$command	= array();
			$todayDate	= new JDate();
			
			if( $mode == 'insert' ) //new user params
			{				
				
				$inserted	= false;				
			
				$uApps	= EasyBlogHelper::getTable( 'UserApps' , 'Table' );
				$uApps->app_id		= $appId;
				$uApps->user_id		= $userId;
				$uApps->created 	= $todayDate->toMySql();
				$uApps->modified	= $todayDate->toMySql();
				$uApps->published	= true;
				if($uApps->store()) $inserted = true;
				
				if($inserted)
				{					
					$tmpId	= $uApps->id;					
					foreach ($param as $key => $val)
					{
						$query	= 'INSERT INTO ' . $db->nameQuote('#__easyblog_params') . ' '
								. ' ( ' 
									. $db->nameQuote('refer_id') . ', '
									. $db->nameQuote('param_name') . ', '
									. $db->nameQuote('param_value') .', '
									. $db->nameQuote('param_value_type') . ', '
									. $db->nameQuote('param_type') . ', '
									. $db->nameQuote('created') . ', ' 
									. $db->nameQuote('modified') . ', '
									. $db->nameQuote('published') . ', ' 
									. $db->nameQuote('ordering') . ' '
								. ' ) '	
								. 'VALUES ' 
								. ' ( '
									. $db->quote($tmpId) . ', '
									. $db->quote($key) . ', '
									. $db->quote($val) .', '
									. $db->quote('string') . ', '
									. $db->quote('userapp') . ', '
									. $db->quote($todayDate->toMySql()) .', '
									. $db->quote($todayDate->toMySql()) .', '
									. $db->quote('1') . ', ' 
									. $db->quote('1') . ' '
								. ' ); ';
						$command[]	= $query;
					}
				}
			}
			else //updating
			{
				foreach ($param as $key => $val)
				{
					$query	= 'UPDATE ' . $db->nameQuote('#__easyblog_params') . ' '
							. 'SET '	
								. $db->nameQuote('param_value') .' = ' . $db->quote($val) . ', '
								. $db->nameQuote('modified') . ' = ' . $db->Quote($todayDate->toMySql()) . ' '
							. 'WHERE '
								. $db->nameQuote('refer_id') . ' = ' . $db->quote($referId) . ' AND '
								. $db->nameQuote('param_name') . ' = ' . $db->quote($key) . ' AND '
								. $db->nameQuote('param_type') . ' = ' . $db->quote('userapp');
					$command[]	= $query;		
				}
			}
			
			// now we execute all the query
			if(empty($command)) $result = false;
			
			foreach ($command as $cmdQuery)
			{
				$db->setQuery($cmdQuery);
				if(! $db->Query()) {
					$result = false;
				}
				
				if($db->getErrorNum()){
					JError::raiseError( 500, $db->stderr());
				}			
			}						
			//process ended here.									
		}
		else
		{
			$result = false;
		}				
		return $result;
	}

}
