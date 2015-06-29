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

defined('_JEXEC') or die('Restricted access');
//error_reporting(E_STRICT);

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

/*
$lang = JFactory::getLanguage();
if ($lang->get('lang') != 'en-GB') {
    $lang = JFactory::getLanguage();
    $lang->load('com_djcatalog2', JPATH_ROOT, 'en-GB', false, false);
    $lang->load('com_djcatalog2', JPATH_COMPONENT, 'en-GB', false, false);
    $lang->load('com_djcatalog2', JPATH_ROOT, null, true, false);
    $lang->load('com_djcatalog2', JPATH_COMPONENT, null, true, false);
}
*/

require_once(JPATH_COMPONENT.DS.'defines.djcatalog2.php');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'lib'.DS.'categories.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'image.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'file.php');

require_once(JPATH_COMPONENT.DS.'controller.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'route.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'html.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'theme.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'djcatalog2.php');

$controller = JControllerLegacy::getInstance('Djcatalog2');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

?>

