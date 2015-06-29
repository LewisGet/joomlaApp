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

/**
 * Content Component Article Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class EasyBlogModelTeamBlogs extends JModel
{
	var $_data	= null;
	var $_total = null;
	var $_pagination = null;

	function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		//$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');

		$limit		= EasyBlogHelper::getHelper( 'Pagination' )->getLimit();
		$limitstart = JRequest::getInt('limitstart', 0, 'REQUEST');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination	= EasyBlogHelper::getPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= $this->getDBO();

		$query	= 'SELECT a.* FROM ' . $db->nameQuote( '#__easyblog_team' ) . ' AS a '
				. 'LEFT JOIN `#__easyblog_team_users` AS b ON a.`id` = b.`team_id` '
				. 'LEFT JOIN `#__easyblog_team_groups` AS c ON a.`id` = c.`team_id` '
				. $where . ' '
				. 'GROUP BY a.`id` HAVING (count(b.`team_id`) > 0 || count(c.`team_id`) > 0 ) '
				. $orderby;

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= $this->getDBO();

		$where[]	= 'a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' );
		$where		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}


	function _buildQueryOrderBy()
	{

		$orderby 	= ' ORDER BY a.`title` ASC';

		return $orderby;
	}

	/**
	 * Method to get teamblog item data
	 *
	 * @access public
	 * @return array
	 */
	function getTeamBlogs()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Retrieves a list of team blogs created by the specific users.
	 *
	 * @param int $userId
	 */
	public function getUserTeams( $userId )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT a.* FROM ' . $db->nameQuote( '#__easyblog_team' ) . ' AS a '
				. 'LEFT JOIN `#__easyblog_team_users` AS b ON a.`id` = b.`team_id` '
				. 'LEFT JOIN `#__easyblog_team_groups` AS c ON a.`id` = c.`team_id` '
				. 'WHERE a.' . $db->nameQuote( 'published' ) . ' = ' . $db->Quote( 1 ) . ' '
				. 'AND a.' . $db->nameQuote( 'created_by' ) . ' = ' . $db->Quote( $userId ) . ' '
				. 'GROUP BY a.`id` HAVING (count(b.`team_id`) > 0 || count(c.`team_id`) > 0 )';
		$db->setQuery( $query );

		$rows	= $db->loadObjectList();

		if( !$rows )
		{
			return;
		}

		$teams	= array();


		foreach( $rows as $row )
		{
			$team	= EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
			$team->bind( $row );

			$teams	= $team;
		}
		return $teams;
	}

	/**
	 * Method to get the total nr of the team
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Retrieve a list of team members from a specific team blog.
	 *
	 * @params	int	$teamId
	 */
	function getTeamMembers( $teamId )
	{
		$db 	= $this->getDBO();

		$query	= 'SELECT ' . $db->nameQuote( 'user_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__easyblog_team_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'team_id' ) . ' = ' . $db->Quote( $teamId );
		$db->setQuery($query);

		$result 	= $db->loadObjectList();

		// @rule: Process users from Joomla user groups
		$exclusion	= '';
		$total		= count( $result );

		if( $result )
		{
			for( $i = 0; $i < $total; $i++ )
			{
				$exclusion .= $db->Quote( $result[ $i ]->user_id );

				if( next( $result ) !== false )
				{
					$exclusion .= ',';
				}
			}
		}

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$query	= 'SELECT b.`user_id` '
					. 'FROM ' . $db->nameQuote( '#__easyblog_team_groups' ) . ' AS a '
					. 'INNER JOIN ' . $db->nameQuote( '#__user_usergroup_map' ) . ' AS b '
					. 'ON a.`group_id` = b.`group_id` '
					. 'WHERE a.' . $db->nameQuote( 'team_id' ) . ' = ' . $db->Quote( $teamId );

			if( !empty( $exclusion ) )
			{
				$query	.= ' AND b.`user_id` NOT IN(' . $exclusion . ')';
			}
		}
		else
		{
			$query	= 'SELECT c.' . $db->nameQuote( 'value' ) . ' AS `user_id` '
					. 'FROM ' . $db->nameQuote( '#__easyblog_team_groups' ) . ' AS a '
					. 'LEFT JOIN ' . $db->nameQuote( '#__core_acl_groups_aro_map' ) . ' AS b '
					. 'ON a.`group_id` = b.`group_id` '
					. 'INNER JOIN ' . $db->nameQuote( '#__core_acl_aro' ) . ' AS c '
					. 'ON b.' . $db->nameQuote( 'aro_id' ) . ' = c.' . $db->nameQuote( 'id' )
					. 'WHERE a.' . $db->nameQuote( 'team_id' ) . ' = ' . $db->Quote( $teamId );

			if( !empty( $exclusion ) )
			{
				$query	.= ' AND c.`value` NOT IN(' . $exclusion . ')';
			}
		}
		$db->setQuery( $query );

		$groupUsers	= $db->loadObjectList();
		$result		= array_merge( $result, $groupUsers );

		return $result;
	}

	function getTotalTeamJoined( $userId )
	{
		$db = $this->getDBO();

		$query  = 'select count(1) from `#__easyblog_team_users` where `user_id` = ' . $db->Quote($userId);
		$db->setQuery( $query );

		$result	= $db->loadResult();
		return (empty($result)) ? 0 : $result;
	}

	/*
	 * Retrieve a list of team blogs joined by the specified user.
	 *
	 * @param	int		$userId		The specified user subject.
	 * @return	array	An array of TeamBlogTable data.
	 */
	function getTeamJoined( $userId )
	{
		$db 	= $this->getDBO();

		$query	= 'SELECT b.*, ' . $db->Quote( '0' ) . ' AS ' . $db->nameQuote( 'selected' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__easyblog_team_users' ) . ' AS `a` '
				. 'LEFT JOIN ' . $db->nameQuote( '#__easyblog_team' ) . ' AS `b` '
				. 'ON `a`.' . $db->nameQuote( 'team_id' ) . '= b.' . $db->nameQuote( 'id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'user_id' ) .'=' . $db->Quote( $userId ) . ' '
				. 'AND b.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );

		$db->setQuery( $query );

		$result	= $db->loadObjectList();

		// @rule: Add exclusions when searching for Joomla user groups
		$exclusions	= '';
		if( $result )
		{
			$total	= count( $result );
			for( $i = 0; $i < $total; $i++ )
			{
				$exclusions	.= $db->Quote( $result[ $i ]->id );

				if( next( $result ) !== false )
				{
					$exclusions	.= ',';
				}
			}
		}

		// @rule: Check if this user is assigned to any Joomla user groups
		$query	= 'SELECT b.*, ' . $db->Quote( 0 ) . ' AS ' . $db->nameQuote( 'selected' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__easyblog_team_groups' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__easyblog_team' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'team_id' ) . ' = b.' . $db->nameQuote( 'id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'group_id' ) . ' IN (';

		$groups	= EasyBlogHelper::getUserGids( $userId );

		if( !is_array( $groups ) )
		{
			$groups	= array( $groups );
		}

		$total	= count( $groups );
		for( $i = 0; $i < $total; $i++ )
		{
			$query	.= $db->Quote( $groups[ $i ] );

			if( next( $groups ) !== false )
			{
				$query	.= ',';
			}
		}

		$query	.= ')';
		$query	.= ' AND b.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );

		if( !empty( $exclusions ) )
		{
			$query	.= ' AND b.`id` NOT IN(' . $exclusions . ')';
		}

		$db->setQuery( $query );

		$groupResult	= $db->loadObjectList();

		// @rule: Merge the normal members team and group teams.
		$result	= array_merge( $result , $groupResult );

		if( !$result )
		{
			return false;
		}

		$teams	= array();
		JTable::addIncludePath( EBLOG_TABLES );

		foreach( $result as $row )
		{
			$team		= EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
			$team->bind( $row );

			$teams[]	= $team;
		}
		return $teams;
	}

	function getBlogContributed( $postId )
	{
		$db = $this->getDBO();

		$query  = 'SELECT a.`team_id`, b.`title`, \'1\' AS `selected`';
		$query	.= ' FROM `#__easyblog_team_post`  AS `a`';
		$query  .= ' LEFT JOIN `#__easyblog_team` AS `b` ON a.`team_id` = b.`id`';
		$query	.= ' WHERE a.`post_id` = ' . $db->Quote($postId);

		$db->setQuery( $query );
		$result	= $db->loadObject();

		return $result;
	}

	function checkIsTeamAdmin($userId , $teamId	= '')
	{
		$db = $this->getDBO();

		$query  = 'select count(1) from `#__easyblog_team_users` as a';
		$query  .= ' inner join `#__easyblog_team` as b on a.`team_id` = b.`id`';
		$query  .= ' where a.`user_id` = ' . $db->Quote($userId);
		$query  .= ' and a.`isadmin` = ' . $db->Quote('1');
		if(!empty($teamId))
			$query  .= ' and a.`team_id` = ' . $db->Quote($teamId);

		$db->setQuery($query);
		$result	= $db->loadResult();

		return ($result > 0) ? true : false;
	}

	function getTotalRequest()
	{
		$my     = JFactory::getUser();

		$userId	= (EasyBlogHelper::isSiteAdmin()) ? '' : $my->id;
		return count($this->getTeamBlogRequest($userId, false));
	}

	function getTeamBlogRequest($userId = '', $useLimit = true)
	{
		$db = $this->getDBO();

		$limit		= $this->getState('limit');
		$limitstart = $this->getState('limitstart');

		// common query
		$cquery = '';
		if(! empty($userId))
		{
			$cquery  .= ' inner join `#__easyblog_team_users` as b';
			$cquery  .= '    on a.`team_id` = b.`team_id`';
			$cquery  .= '    and b.`user_id` = ' . $db->Quote($userId);
			$cquery  .= '    and b.`isadmin` = ' . $db->Quote('1');
		}
		$cquery  .= '  inner join `#__easyblog_team` as c on a.`team_id` = c.`id`';
		$cquery  .= ' where a.`ispending` = ' . $db->Quote('1');

		$query  = 'select count(1) from `#__easyblog_team_request` as a';
		$query  .= $cquery;


		$db->setQuery( $query );
		$this->_total	= $db->loadResult();

		jimport('joomla.html.pagination');
		$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );
		//actual query
		$query  = 'select a.*, c.`title` from `#__easyblog_team_request` as a';
		$query  .= $cquery;
		$query  .= ' order by a.`created`';
		if($useLimit)
			$query	.= ' LIMIT ' . $limitstart . ',' . $limit;


		$db->setQuery($query);
		$result	= $db->loadObjectList();

		return $result;
	}

	function isTeamSubscribedUser($teamId, $userId, $email)
	{
		$db	= $this->getDBO();

		$query  = 'SELECT `id` FROM `#__easyblog_team_subscription`';
		$query  .= ' WHERE `team_id` = ' . $db->Quote($teamId);
		$query  .= ' AND (`user_id` = ' . $db->Quote($userId);
		$query  .= ' OR `email` = ' . $db->Quote($email) .')';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function isTeamSubscribedEmail($teamId, $email)
	{
		$db	= $this->getDBO();

		$query  = 'SELECT `id` FROM `#__easyblog_team_subscription`';
		$query  .= ' WHERE `team_id` = ' . $db->Quote($teamId);
		$query  .= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function addTeamSubscription($teamId, $email, $userId = '0', $fullname = '')
	{
		$config = EasyBlogHelper::getConfig();
		$acl = EasyBlogACLHelper::getRuleSet();
		$my = JFactory::getUser();

		$teamTbl	= EasyBlogHelper::getTable( 'Teamblog', 'Table' );
		$teamTbl->load($teamId);
		$gid		= EasyBlogHelper::getUserGids($userId);
		$isMember	= $teamTbl->isMember($userId, $gid);

		if($teamTbl->allowSubscription($teamTbl->access, $userId, $isMember, $acl->rules->allow_subscription))
		{
			$date       = JFactory::getDate();
			$subscriber = EasyBlogHelper::getTable( 'TeamSubscription', 'Table' );

			$subscriber->team_id 	= $teamId;
			$subscriber->email    	= $email;
			if($userId != '0')
				$subscriber->user_id    = $userId;

			$subscriber->fullname	= $fullname;
			$subscriber->created  	= $date->toMySQL();
			$subscriber->store();
		}
	}

	function updateTeamSubscriptionEmail($sid, $userId, $email)
	{
		$config = EasyBlogHelper::getConfig();
		$acl = EasyBlogACLHelper::getRuleSet();
		$my = JFactory::getUser();

		$subscriber = EasyBlogHelper::getTable( 'TeamSubscription', 'Table' );
		$subscriber->load($sid);

		$teamTbl	= EasyBlogHelper::getTable( 'Teamblog', 'Table' );
		$teamTbl->load($subscriber->team_id);

		$gid		= EasyBlogHelper::getUserGids($userId);
		$isMember	= $teamTbl->isMember($userId, $gid);

		if($teamTbl->allowSubscription($teamTbl->access, $userId, $isMember, $acl->rules->allow_subscription))
		{
			$subscriber->user_id  = $userId;
			$subscriber->email    = $email;
			$subscriber->store();
		}
	}

	function getTeamSubscribers($teamId)
	{
		$db = JFactory::getDBO();

		$query  = "SELECT *, 'teamsubscription' as `type` FROM `#__easyblog_team_subscription`";
		$query  .= " WHERE `team_id` = " . $db->Quote($teamId);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	function getPostTeamId($id)
	{
		$db = JFactory::getDBO();

		$query  = "SELECT `team_id` FROM `#__easyblog_team_post`";
		$query  .= " WHERE `post_id` = " . $db->Quote($id);

		$db->setQuery($query);
		return $db->loadResult();
	}
}
