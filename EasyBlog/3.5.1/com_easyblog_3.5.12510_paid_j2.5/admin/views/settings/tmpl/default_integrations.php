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
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_EASYDISCUSS' ) , 'easydiscuss');
echo $this->loadTemplate( 'integrations_easydiscuss' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_MIGHTYTOUCH' ) , 'mightytouch');
echo $this->loadTemplate( 'integrations_mightytouch' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_JOMSOCIAL' ) , 'jomsocial');
echo $this->loadTemplate( 'integrations_jomsocial' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_AUP' ) , 'aup');
echo $this->loadTemplate( 'integrations_aup' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_PHOCAPDF' ) , 'phocapdf');
echo $this->loadTemplate( 'integrations_phocapdf' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_ADSENSE' ) , 'adsense');
echo $this->loadTemplate( 'integrations_adsense' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_ZEMANTA' ) , 'zemanta');
echo $this->loadTemplate( 'integrations_zemanta' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_PINGOMATIC' ) , 'pingomatic');
echo $this->loadTemplate( 'integrations_pingomatic' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_FLICKR' ) , 'flickr');
echo $this->loadTemplate( 'integrations_flickr' );
echo $pane->endPanel();
echo $pane->endPane();
