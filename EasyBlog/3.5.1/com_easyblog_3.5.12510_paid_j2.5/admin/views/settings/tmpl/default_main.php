<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$pane	= JPane::getInstance('Tabs');

echo $pane->startPane("submain");
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_GENERAL' ) , 'general');
echo $this->loadTemplate( 'main_general' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_RSS' ) , 'rss');
echo $this->loadTemplate( 'main_rss' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_SUBSCRIPTIONS' ) , 'subscriptions');
echo $this->loadTemplate( 'main_subscriptions' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_REMOTE_PUBLISHING' ) , 'remote_publishing');
echo $this->loadTemplate( 'main_remote_publishing' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_LOCATION' ) , 'location');
echo $this->loadTemplate( 'main_location' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_MICROBLOGGING' ) , 'microblog');
echo $this->loadTemplate( 'main_microblog' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_RATINGS' ) , 'ratings');
echo $this->loadTemplate( 'main_ratings' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_AUTODRAFTS' ) , 'autodrafts');
echo $this->loadTemplate( 'main_autodrafts' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBTAB_MAINTENANCE' ) , 'maintenance');
echo $this->loadTemplate( 'main_maintenance' );
echo $pane->endPanel();
echo $pane->endPane();