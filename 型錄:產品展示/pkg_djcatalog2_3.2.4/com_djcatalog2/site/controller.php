<?php
/**
 * @version $Id: controller.php 92 2012-11-07 10:36:34Z michal $
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
defined ('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');

class DJCatalog2Controller extends JControllerLegacy
{

	function __construct($config = array())
	{
		parent::__construct($config);
		$lang = JFactory::GetLanguage();
		$lang->load('com_djcatalog2');
		$this->registerTask( 'modfp',  'getFrontpageXMLData' );
	}

	function display($cachable = true, $urlparams = null)
	{
		DJCatalog2ThemeHelper::setThemeAssets();

		parent::display($cachable, $urlparams);
	}
	function getFrontpageXMLData() {
		$model = $this->getModel('modfrontpage');
		$xml = $model->getXml();
		echo $xml;
		exit;
	}
	function search() {
		$app = JFactory::getApplication();
		$post = JRequest::get('post');
		$params = array();
		foreach($post as $key => $value) {
			if ($key != 'task' && $key != 'option' && $key != 'view' && $key != 'cid' && $key != 'Itemid') {
				if ($key == 'search') {
					$params[] = $key.'='.urlencode($value);
				}
				else if (is_array($value)) {
					foreach ($value as $k => $v) {
						$params[] = $key.'[]='.$v;
					}
				}
				else {
					$params[] = $key.'='.$value;
				}
			}
		}
		
		if (!array_key_exists('cm', $post)) {
			$params[] = 'cm=0';
		}

		$uri = JRoute::_( DJCatalogHelperRoute::getCategoryRoute( JRequest::getVar( 'cid','0','default','string' )), false );

		if (strpos($uri,'?') === false ) {
			$get = (count($params)) ? '?'.implode('&',$params) : '';
		} else {
			$get = (count($params)) ? '&'.implode('&',$params) : '';
		}
		
		$app->redirect( $uri.$get.'#tlb' );
	}
	function download() {
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$authorised = $user->authorise('djcatalog2.filedownload', 'com_djcatalog2');
		
		if ($authorised !== true) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		if (!DJCatalog2FileHelper::getFile(JRequest::getVar('fid','','default','int'))){
            JError::raiseError(404);
            return false;
        }
        
        /*
		if ($out = DJCatalog2FileHelper::getFile(JRequest::getVar('fid','','default','int'))) {
			JRequest::setVar('format','raw');
			echo $out;
		}
		
		else {
			JError::raiseError(404);
			return false;
		}*/
	}
}