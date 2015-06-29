<?php
/**
 * @version $Id: djcatalog2.php 105 2013-01-23 14:05:57Z michal $
 * @package DJ-Catalog2
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Michal Olczyk - michal.olczyk@design-joomla.eu
 *
 * DJ-Catalog2 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-Catalog2 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-Catalog2. If not, see <http://www.gnu.org/licenses/>.
 *
 */

// No direct access
defined('_JEXEC') or die;

class Djcatalog2Helper
{
	public static function addSubmenu($vName = 'cpanel')
	{
		$version = new JVersion;

		if (version_compare($version->getShortVersion(), '3.0.0', '<')) {
			JSubMenuHelper::addEntry(JText::_('COM_DJCATALOG2_CPANEL'), 'index.php?option=com_djcatalog2&view=cpanel', $vName=='cpanel');
			JSubMenuHelper::addEntry(JText::_('COM_DJCATALOG2_ITEMS'), 'index.php?option=com_djcatalog2&view=items', $vName=='items');
			JSubMenuHelper::addEntry(JText::_('COM_DJCATALOG2_CATEGORIES'), 'index.php?option=com_djcatalog2&view=categories', $vName=='categories');
			JSubMenuHelper::addEntry(JText::_('COM_DJCATALOG2_PRODUCERS'), 'index.php?option=com_djcatalog2&view=producers', $vName=='producers');
			JSubMenuHelper::addEntry(JText::_('COM_DJCATALOG2_FIELDGROUPS'), 'index.php?option=com_djcatalog2&view=fieldgroups', $vName=='fieldgroups');
			JSubMenuHelper::addEntry(JText::_('COM_DJCATALOG2_FIELDS'), 'index.php?option=com_djcatalog2&view=fields', $vName=='fields');
			JSubMenuHelper::addEntry(JText::_('COM_DJCATALOG2_IMAGES_MANAGER'), 'index.php?option=com_djcatalog2&view=thumbs', $vName=='thumbs');
		} else {
			JHtmlSidebar::addEntry(JText::_('COM_DJCATALOG2_CPANEL'), 'index.php?option=com_djcatalog2&view=cpanel', $vName=='cpanel');
			JHtmlSidebar::addEntry(JText::_('COM_DJCATALOG2_ITEMS'), 'index.php?option=com_djcatalog2&view=items', $vName=='items');
			JHtmlSidebar::addEntry(JText::_('COM_DJCATALOG2_CATEGORIES'), 'index.php?option=com_djcatalog2&view=categories', $vName=='categories');
			JHtmlSidebar::addEntry(JText::_('COM_DJCATALOG2_PRODUCERS'), 'index.php?option=com_djcatalog2&view=producers', $vName=='producers');
			JHtmlSidebar::addEntry(JText::_('COM_DJCATALOG2_FIELDGROUPS'), 'index.php?option=com_djcatalog2&view=fieldgroups', $vName=='fieldgroups');
			JHtmlSidebar::addEntry(JText::_('COM_DJCATALOG2_FIELDS'), 'index.php?option=com_djcatalog2&view=fields', $vName=='fields');
			JHtmlSidebar::addEntry(JText::_('COM_DJCATALOG2_IMAGES_MANAGER'), 'index.php?option=com_djcatalog2&view=thumbs', $vName=='thumbs');
		}
	}

	public static function getActions($asset = null, $assetId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if ( !$asset) {
			$assetName = 'com_djcatalog2';
		} else if ($assetId != 0){
			$assetName = 'com_djcatalog2.'.$asset.$assetId;
		} else {
			$assetName = 'com_djcatalog2.'.$asset;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);
		
		$actions = array(
			'catalog2.admin','core.admin'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
}
