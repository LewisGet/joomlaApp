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
require_once( JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'constants.php' );
require_once( EBLOG_HELPERS . DS . 'router.php' );

/**
 * Content Component Article Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class EasyBlogModelArchive extends JModel
{

	/**
	 * Record total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');
	    $limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getArchive( $archiveYear, $archiveMonth, $archiveDay='')
	{
		$db	= JFactory::getDBO();
		$my = JFactory::getUser();

		$config         = EasyBlogHelper::getConfig();
		$isBloggerMode  = EasyBlogRouter::isBloggerMode();
		$excludeCats	= array();
		$teamBlogIds    = '';
		$queryExclude   = '';

		//where
		$queryWhere	= ' WHERE a.`published` = 1';
		$queryWhere	.= ' AND a.`ispending` = 0';


	    //get teamblogs id.
	    $query  = '';
	    if( $config->get( 'main_includeteamblogpost' ) )
	    {
			$teamBlogIds	= EasyBlogHelper::getViewableTeamIds();
			if( count( $teamBlogIds ) > 0 )
            	$teamBlogIds    = implode( ',' , $teamBlogIds);
	    }

	    //var_dump($teamBlogIds);
	    $excludeCats	= EasyBlogHelper::getPrivateCategories();

		if(! empty($excludeCats))
		{
		    $queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		//do not list out protected blog in rss
		if(JRequest::getCmd('format', '') == 'feed')
		{
			if($config->get('main_password_protect', true))
			{
				$queryWhere	.= ' AND a.`blogpassword`="" ';
			}
		}

		//blog privacy setting
		// @integrations: jomsocial privacy
		$file		= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';

		if( $config->get( 'main_jomsocial_privacy' ) && JFile::exists( $file ) && !EasyBlogHelper::isSiteAdmin())
		{
			require_once( $file );

			$my			= JFactory::getUser();
			$jsFriends	= CFactory::getModel( 'Friends' );
			$friends	= $jsFriends->getFriendIds( $my->id );

			// Insert query here.
			$queryWhere	.= ' AND (';
			$queryWhere	.= ' (a.`private`= 0 ) OR';
			$queryWhere	.= ' ( (a.`private` = 20) AND (' . $db->Quote( $my->id ) . ' > 0 ) ) OR';

			if( empty( $friends ) )
			{
				$queryWhere	.= ' ( (a.`private` = 30) AND ( 1 = 2 ) ) OR';
			}
			else
			{
				$queryWhere	.= ' ( (a.`private` = 30) AND ( a.' . $db->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$queryWhere	.= ' ( (a.`private` = 40) AND ( a.' . $db->nameQuote( 'created_by' ) .'=' . $my->id . ') )';
			$queryWhere	.= ' )';
		}
		else
		{
			if( $my->id == 0)
			{
				$queryWhere .= ' AND a.`private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
			}
		}

	    if( $config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds))
	    {
			$queryWhere	.= ' AND (u.team_id IN ('.$teamBlogIds.') OR a.`issitewide` = ' . $db->Quote('1') . ')';
		}
		else
		{
		    $queryWhere	.= ' AND a.`issitewide` = ' . $db->Quote('1');
		}


		if(empty($archiveDay))
		{
			$fromDate	= $archiveYear.'-'.$archiveMonth.'-01 00:00:00';
			$toDate		= $archiveYear.'-'.$archiveMonth.'-31 23:59:59';
		}
		else
		{
			$fromDate	= $archiveYear.'-'.$archiveMonth.'-'.$archiveDay.' 00:00:00';
			$toDate		= $archiveYear.'-'.$archiveMonth.'-'.$archiveDay.' 23:59:59';
		}

		$tzoffset   = EasyBlogDateHelper::getOffSet( true );
		$queryWhere	.= ' AND ( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) >= '. $db->Quote($fromDate) .' AND DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) <= '. $db->Quote($toDate) . ' ) ';

		if($isBloggerMode !== false)
		    $queryWhere .= ' AND a.`created_by` = ' . $db->Quote($isBloggerMode);

		//ordering
		$queryOrder	= ' ORDER BY a.`created` DESC';

		//limit
		$limit		= $this->getState('limit');
		$limitstart = $this->getState('limitstart');
		$queryLimit	= ' LIMIT ' . $limitstart . ',' . $limit;

		//set pagination
		$query	= 'SELECT COUNT(1) FROM `#__easyblog_post` AS a';
		$query .= ' LEFT JOIN `#__easyblog_category` AS b';
		$query .= ' ON a.category_id = b.id';

		if( $config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds) )
		{
		    $query  .= ' LEFT JOIN `#__easyblog_team_post` AS u ON a.id = u.post_id';
		}

		$query	.= $queryWhere;
		$db->setQuery( $query );
		$this->_total	= $db->loadResult();
		jimport('joomla.html.pagination');
		$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );
		
		//get archive
		$query	= 'SELECT a.*, b.`title` AS `category`';
		if( $config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds) )
		    $query  .= ' ,u.team_id';


		$query .= ' FROM `#__easyblog_post` AS a';
		$query .= ' LEFT JOIN `#__easyblog_category` AS b';
		$query .= ' ON a.category_id = b.id';

		if( $config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds) )
		{
		    $query  .= ' LEFT JOIN `#__easyblog_team_post` AS u ON a.id = u.post_id';
		}


		$query .= $queryWhere;
		$query .= $queryExclude;
		$query .= $queryOrder;
		$query .= $queryLimit;

		// echo $query . '<br><br>';

		$db->setQuery($query);
		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		$result	= $db->loadObjectList();
		return $result;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		return $this->_pagination;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		return $this->_total;
	}

    function getArchiveMinMaxYear()
	{
		$db 	= JFactory::getDBO();
		$user	= JFactory::getUser();

		$query	= 'SELECT YEAR(MIN( '.$db->nameQuote('created').' )) AS minyear, '
				. 'YEAR(MAX( '.$db->nameQuote('created').' )) AS maxyear '
				. 'FROM '.$db->nameQuote('#__easyblog_post').' '
				. 'WHERE '.$db->nameQuote('published').' = '.$db->Quote(true).' ';

		if(empty($user->id))
		{
			$query .= 'AND '.$db->nameQuote('private').' = '.$db->Quote(false).' ';
		}

		$db->setQuery($query);
		$row = $db->loadAssoc();

		if(empty($row['minyear']) || empty($row['maxyear']))
		{
			$year = array();
		}
		else
		{
			$year = $row;
		}

		return $year;
	}

	function getArchivePostCount($yearStart='', $yearStop='0', $excludeCats = '')
	{
		$result = self::getArchivePostCounts($yearStart, $yearStop, $excludeCats, '');
		return $result;
	}

	function getArchivePostCounts($yearStart='', $yearStop='0', $excludeCats = '', $includeCats = '')
	{
		$db 	= JFactory::getDBO();
		$user	= JFactory::getUser();

		if(empty($yearStart))
		{
			$year		= $this->getArchiveMinMaxYear();
			$yearStart	= $year['maxyear'];
		}

		if(!empty($yearStop))
		{
			$fr = $yearStart - 1;
			$to	= $yearStop + 1;
		}
		else
		{
			$fr = $yearStart - 1;
			$to	= $yearStart + 1;
		}

		if( !is_array( $excludeCats ) && !empty( $excludeCats ) )
		{
			$excludeCats	= explode( ',' , $excludeCats );
		}
		else if( !is_array( $excludeCats ) && empty( $excludeCats ) )
		{
			$excludeCats    = array();
		}


		if( !is_array( $includeCats ) && !empty( $includeCats ) )
		{
			$includeCats	= explode( ',' , $includeCats );
		}
		else if( !is_array( $includeCats ) && empty( $includeCats ) )
		{
			$includeCats    = array();
		}

		$includeCats    = array_diff( $includeCats, $excludeCats );

		$excludeCatIds = '';
		if( !empty( $excludeCats ) && count( $excludeCats ) >= 1 )
		{
			foreach($excludeCats as $cat)
			{
				if( trim($cat) != '')
				{
					$excludeCatIds = empty( $excludeCatIds ) ? $db->Quote($cat) : $excludeCatIds . ',' . $db->Quote($cat);
				}
			}
		}

		$includeCatIds = '';
		if( !empty( $includeCats ) && count( $includeCats ) >= 1 )
		{
			foreach($includeCats as $icat)
			{
				if( trim($icat) != '')
				{
					$includeCatIds = empty( $includeCatIds ) ? $db->Quote($icat) : $includeCatIds . ',' . $db->Quote($icat);
				}
			}
		}

		$privateBlog = empty($user->id)? 'AND a.'.$db->nameQuote('private').' = '.$db->Quote(false) : '';

		// Test for category permissions too
		if( $user->id <= 0 )
		{
			$privateBlog	.= ' AND b.' . $db->nameQuote( 'private' ) . '=' . $db->Quote( '0' );
		}

		$catExcludeSQL = (! empty($excludeCatIds)) ? 'AND `category_id` NOT IN ('.$excludeCatIds.')' : '';

		$catIncludeSQL = (! empty($includeCatIds)) ? 'AND `category_id` IN ('.$includeCatIds.')' : '';

		$query	= 'SELECT COUNT(1) as count, MONTH( a.'.$db->nameQuote('created').' ) AS month, YEAR( a.'.$db->nameQuote('created').' ) AS year '
				. 'FROM '.$db->nameQuote('#__easyblog_post').' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__easyblog_category') . ' AS b '
				. 'ON a.' . $db->nameQuote( 'category_id' ) . '=b.' . $db->nameQuote( 'id' ) . ' '
				. 'WHERE a.'.$db->nameQuote('published').' = '.$db->Quote(POST_ID_PUBLISHED).' '
				. $privateBlog.' '
				. $catExcludeSQL.' '
				. $catIncludeSQL.' '
				. 'AND ( a.'.$db->nameQuote('created').' > '.$db->Quote($fr.'-12-31 23:59:59').' AND a.'.$db->nameQuote('created').' < '.$db->Quote($to.'-01-01 00:00:00').') '
				. 'GROUP BY year, month DESC '
				. 'ORDER BY a.'.$db->nameQuote('created').' DESC ';

		$db->setQuery($query);
		$row = $db->loadAssocList();


		if(empty($row))
		{
			return false;
		}

		$postCount = new stdClass();
		foreach($row as $data)
		{
			$postCount->{$data['year']}->{$data['month']} = $data['count'];
		}

		return $postCount;
	}


	function getArchivePostCountByMonth($month='', $year='', $showPrivate=true)
	{
		$db 	= JFactory::getDBO();
		$user	= JFactory::getUser();

		$privateBlog = $showPrivate? '' : 'AND '.$db->nameQuote('private').' = '.$db->Quote(false);

		$tzoffset   = EasyBlogDateHelper::getOffSet( true );
		$query	= 'SELECT COUNT(1) as count, DAY( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS day,';
		$query	.= ' MONTH( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS month,';
		$query	.= ' YEAR( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS year ';
		$query	.= ' FROM '.$db->nameQuote('#__easyblog_post');
		$query	.= ' WHERE '.$db->nameQuote('published').' = '.$db->Quote(POST_ID_PUBLISHED);
		$query	.= ' ' . $privateBlog;
		$query	.= ' AND ('.$db->nameQuote('created').' > '.$db->Quote($year.'-'.$month.'-01 00:00:00').' AND '.$db->nameQuote('created').' < '.$db->Quote($year.'-'.$month.'-31 23:59:59').')';
		$query	.= ' GROUP BY day, year, month ';
		$query	.= ' ORDER BY '.$db->nameQuote('created').' ASC ';

		$db->setQuery($query);
		$row = $db->loadAssocList();

		$postCount = new stdClass();

		for($i=1; $i<=31; $i++)
		{
			$postCount->{$year}->{$month}->{$i} = 0;
		}

		if(!empty($row))
		{
			foreach($row as $data)
			{
				$postCount->{$year}->{$month}->{$data['day']} = $data['count'];
			}
		}

		return $postCount;
	}

	function getArchivePostByMonth( $month='', $year='', $showPrivate=true )
	{
		$db 	= JFactory::getDBO();
		$user	= JFactory::getUser();
		$config = EasyBlogHelper::getConfig();

		// used for privacy
		$queryWhere             = '';
		$queryExclude			= '';
		$queryExcludePending    = '';
		$excludeCats			= array();

		if( $user->id == 0) $showPrivate = false;

		if( !$showPrivate )
		{
			$excludeCats	= EasyBlogHelper::getPrivateCategories();
		}

		$privateBlog = $showPrivate? '' : 'AND a.`private` = '.$db->Quote('0');

	    //get teamblogs id.
	    $teamBlogIds    = '';
	    $query  		= '';
	    if( $config->get( 'main_includeteamblogpost' ) )
	    {
			$teamBlogIds	= EasyBlogHelper::getViewableTeamIds();
			if( count( $teamBlogIds ) > 0 )
            	$teamBlogIds    = implode( ',' , $teamBlogIds);
	    }

		if(! empty($excludeCats))
		{
		    $queryWhere .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$jsPostIds  = self::getJomSocialPosts();

	    if( $config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds))
	    {
			if( !empty( $jsPostIds ) )
			{
				$tmpIds = implode( ',', $jsPostIds);
				$queryWhere	.= ' AND (u.team_id IN ('.$teamBlogIds.') OR a.id IN (' . $tmpIds . ') OR a.`issitewide` = ' . $db->Quote('1') . ')';
			}
			else
			{
				$queryWhere	.= ' AND (u.team_id IN ('.$teamBlogIds.') OR a.`issitewide` = ' . $db->Quote('1') . ')';
			}
		}
		else
		{
			if( !empty( $jsPostIds ) )
			{
				$tmpIds = implode( ',', $jsPostIds);
				$queryWhere	.= ' AND (a.id IN (' . $tmpIds . ') OR a.`issitewide` = ' . $db->Quote('1') . ')';
			}
			else
			{
		    	$queryWhere	.= ' AND a.`issitewide` = ' . $db->Quote('1');
			}
		}


		$extraSQL   = '';
		$blogger	= EasyBlogRouter::isBloggerMode();
		if( $blogger !== false )
		{
		    $extraSQL   = ' AND a.`created_by` = ' . $db->Quote($blogger);
		}

		$tzoffset   = EasyBlogDateHelper::getOffSet( true );
		$query	= 'SELECT *, DAY( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS day,';
		$query	.= ' MONTH( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS month,';
		$query	.= ' YEAR( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS year ';
		$query  .= ' FROM '.$db->nameQuote('#__easyblog_post') . ' as a';
		if( $config->get( 'main_includeteamblogpost' ) )
		{
		    $query  .= ' LEFT JOIN `#__easyblog_team_post` AS u ON a.id = u.post_id';
		}
		$query  .= ' WHERE a.`published` = '.$db->Quote(true).' ';
		$query  .= $privateBlog.' ';
		$query  .= ' AND (a.`created` > ' . $db->Quote($year.'-'.$month.'-01 00:00:00') . ' AND a.`created` < ' . $db->Quote($year.'-'.$month.'-31 23:59:59').') ';
		$query  .= $extraSQL . ' ';

		$query	.= $queryWhere;
		$query  .= ' ORDER BY a.`created` ASC ';

		$db->setQuery($query);
		$row = $db->loadObjectList();

		$postCount = new EasyblogCalendarObject($month, $year);


		if(!empty($row))
		{
			foreach($row as $data)
			{
				if( $postCount->{ $year }->{ $month }->{$data->day} == 0 )
				{
					$postCount->{$year}->{$month}->{$data->day}	= array( $data );
				}
				else
				{
					array_push( $postCount->{$year}->{$month}->{$data->day} , $data );
				}
			}
		}

		return $postCount;
	}

	function getJomSocialPosts()
	{
		$db = JFactory::getDBO();

		$isJSGrpPluginInstalled	= false;
		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled( 'system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		$isJSInstalled			= false; // need to check if the site installed jomsocial.

		if(JFile::exists(JPATH_ROOT . DS . 'components' . DS. 'com_community' . DS . 'libraries' . DS .'core.php'))
		{
			$isJSInstalled = true;
		}

		$includeJSGrp	= ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ($isEventPluginInstalled && $isJSInstalled ) ? true : false;

		$jsEventPostIds	= array();
		$jsGrpPostIds	= array();

		if( $includeJSEvent )
		{
			$queryEvent	= 'SELECT ' . $db->nameQuote( 'post_id' ) . ' FROM';
			$queryEvent	.= ' ' . $db->nameQuote( '#__easyblog_external' ) . ' AS ' . $db->nameQuote( 'a' );
			$queryEvent	.= ' INNER JOIN' . $db->nameQuote( '#__community_events' ) . ' AS ' . $db->nameQuote( 'b' );
			$queryEvent	.= ' ON ' . $db->nameQuote( 'a' ) . '.uid = ' . $db->nameQuote( 'b' ) . '.id';
			$queryEvent	.= ' AND ' . $db->nameQuote( 'a' ) . '.' . $db->nameQuote( 'source' ) . '=' . $db->Quote( 'jomsocial.event' );
			$queryEvent	.= ' WHERE ' . $db->nameQuote( 'b' ) . '.' . $db->nameQuote( 'permission' ) . '=' . $db->Quote( 0 );

			$db->setQuery($queryEvent);
			$jsEventPostIds		= $db->loadResultArray();
		}

		if( $includeJSGrp )
		{
			$queryJSGrp = 'select `post_id` from `#__easyblog_external_groups` as exg inner join `#__community_groups` as jsg';
			$queryJSGrp .= '      on exg.group_id = jsg.id ';
			$queryJSGrp .= '      where jsg.`approvals` = 0';

			$db->setQuery($queryJSGrp);
			$jsGrpPostIds   = $db->loadResultArray();
		}

		$includePostIds = array();
		if( !empty($jsGrpPostIds) || !empty( $jsEventPostIds ) )
		{
			$includePostIds = array_merge($jsGrpPostIds, $jsEventPostIds);
		}

		return $includePostIds;

	}
}

class EasyblogCalendarObject
{
	public function __construct( $month, $year )
	{
		$this->{$year} = new stdClass();
		$this->{$year}->{$month} = new stdClass();

		for($i=1; $i<=31; $i++)
		{
			$this->{$year}->{$month}->{$i} = 0;
		}
	}
}

