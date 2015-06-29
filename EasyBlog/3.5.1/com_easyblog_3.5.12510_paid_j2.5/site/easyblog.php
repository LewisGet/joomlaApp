<?php
/*
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

// Load constants
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_easyblog' . DS . 'constants.php' );
require_once( EBLOG_ROOT . DS . 'views.php' );
require_once( EBLOG_HELPERS . DS . 'helper.php' );

EasyBlogHelper::getHelper( 'Ajax' )->process();

$config	= EasyBlogHelper::getConfig();

if( $config->get( 'main_error_logging' ) && JRequest::getVar( 'controller' ) != 'xmlrpc' )
{
	require_once( EBLOG_CLASSES . DS . 'exceptional.php' );

	$ssl			= in_array( 'ssl' , stream_get_transports() );

	Exceptional::setup( EBLOG_LOGGING_API , $ssl );
}

require_once( EBLOG_CLASSES . DS . 'ejax.php' );
require_once( EBLOG_CLASSES . DS . 'themes.php' );

$controllerFile = 'controller.php';

require_once( EBLOG_ROOT . DS . $controllerFile );

// Include the tables in path
JTable::addIncludePath( EBLOG_TABLES );

/*
 * Check if there url calling 'cron' or not.
 */
if (JRequest::getCmd('task', '', 'GET') == 'cron')
{
	$mailq	= EasyBlogHelper::getMailQueue();
	$mailq->sendOnPageLoad( $config->get( 'main_mail_total' ) );
	echo 'Email batch process finished. <br />';

	// @task: Process microblogging related stuffs
	EasyBlogHelper::getHelper( 'MicroBlog' )->process();

	// @task: Publish scheduled posts
	EasyBlogHelper::processScheduledPost();

	// @task: Unpublish scheduled post.
	EasyBlogHelper::unPublishPost();

	exit;
}

if (JRequest::getCmd('task', '', 'GET') == 'cronfeed')
{
	// @task: Process rss feed migration
	EasyBlogHelper::getHelper( 'Feeds' )->cron();
	exit;
}

/*
 * Processing email batch sending.
 */
$config = EasyBlogHelper::getConfig();
if ($config->get('main_mailqueueonpageload'))
{
	$mailq	= EasyBlogHelper::getMailQueue( $config->get( 'main_mail_total' ) );
	$mailq->sendOnPageLoad();
}

// @task: Publish scheduled posts
EasyBlogHelper::processScheduledPost();

// @task: Unpublish scheduled post.
EasyBlogHelper::unPublishPost();

$mainframe = JFactory::getApplication();

if(JRequest::getWord('rsd') == 'RealSimpleDiscovery')
{
	$config 		= JFactory::getConfig();
	$title			= $config->get( 'sitename' );
	$link			= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easyblog';
	$xmlrpc			= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easyblog&amp;controller=xmlrpc';

	header( 'Content-Type: text/xml; charset=UTF-8', true );
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?'.'>'; ?>
<rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">
	<service>
		<engineName>EasyBlog!</engineName>
		<engineLink>www.stackideas.com</engineLink>
		<siteName><?php echo $title ?></siteName>
		<homePageLink><?php echo $link ?></homePageLink>
		<apis>
			<api name="EasyBlog" blogID="1" preferred="true" apiLink="<?php echo $xmlrpc;?>" rpcLink="<?php echo $xmlrpc;?>" />
			<api name="MetaWeblog" blogID="1" preferred="false" apiLink="<?php echo $xmlrpc;?>" />
		</apis>
	</service>
</rsd>
<?php
	$mainframe->close();
}
else
{
	/// check the format...else the ajax will failed.
	$document	= JFactory::getDocument();
	$doc_type	= $document->getType();

	if($doc_type == 'html')
	{
		$attribs = array('type' => 'application/rsd+xml', 'title' => 'RSD');

		$xmlLink = JRoute::_('index.php?option=com_easyblog&rsd=RealSimpleDiscovery');
		$document->addHeadLink($xmlLink, 'EditURI', 'rel', $attribs);

		$wlwLink	= rtrim( JURI::root() , '/' ) . '/components/com_easyblog/classes/wlwmanifest.xml';
		$document->addHeadLink( $wlwLink , 'wlwmanifest' , 'rel' , array( 'type' => 'application/wlwmanifest+xml' ) );
	}
}

// Get controller name if specified
$controllerName	= JRequest::getCmd( 'controller' , '' );

// Create controller
$controller	= EasyBlogController::getInstance( $controllerName );

// Perform the Request task
$controller->execute( JRequest::getCmd('task', null, 'default') );

// Redirect if set by the controller
$controller->redirect();
