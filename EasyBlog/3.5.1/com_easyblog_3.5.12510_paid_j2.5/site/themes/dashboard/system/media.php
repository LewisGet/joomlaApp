<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');

$jCfg = JFactory::getConfig();
$joomlaDebug = $jCfg->getValue('debug');
?>

<script type="text/javascript">

EasyBlog.require()
	.script(
		"media"
	)
	.done(function($){

		var MediaManager = $("#MediaManager");

		$("body").html(MediaManager);

		MediaManager
			.implement(
				"EasyBlog.Controller.Media",
				{
					initialPlace: "User",

					directorySeparator: '\<?php echo DS; ?>',

					threadLimit: <?php echo ($joomlaDebug) ? 1 : 8; ?>,

					places: [

						{
							name: "User",
							title: "<?php echo JText::_( 'COM_EASYBLOG_MM_MY_MEDIA' , true );?>",
							options: {
								id: "user:<?php echo $blogger_id ?>",
								uploader: {
									url: '<?php echo JURI::base(); ?>index.php?option=com_easyblog&controller=media&task=upload&tmpl=component&format=json&sessionid=<?php echo $session->getId(); ?>&<?php echo JUtility::getToken();?>=1&bloggger_id=<?php echo $blogger_id; ?>&lang=en',

									max_file_size: '<?php echo $system->config->get( 'main_upload_image_size' );?>mb',
									filters: [{title: "Image files", extensions: "<?php echo $system->config->get( 'main_media_extensions' );?>"}]
								}
							}
						}
						<?php if( $system->config->get( 'main_media_manager_place_shared_media' ) && isset($this->acl->rules->media_places_shared) && $this->acl->rules->media_places_shared ){ ?>
						,
						{
							name: "Shared",
							title: "<?php echo JText::_( 'COM_EASYBLOG_MM_SHARED_MEDIA' );?>",
							options: {
								id: "shared",
								uploader: {
									url: '<?php echo JURI::base(); ?>index.php?option=com_easyblog&controller=media&task=upload&tmpl=component&format=json&sessionid=<?php echo $session->getId(); ?>&<?php echo JUtility::getToken();?>=1&bloggger_id=<?php echo $blogger_id; ?>',
									max_file_size: '<?php echo $system->config->get( 'main_upload_image_size' );?>mb',
									filters: [{title: "Image files", extensions: "<?php echo $system->config->get( 'main_media_extensions' );?>"}]
								}
							}
						}
						<?php } ?>

						<?php if( $system->config->get( 'layout_media_flickr' ) && $system->config->get( 'integrations_flickr_api_key' ) != '' && $system->config->get( 'integrations_flickr_secret_key' ) != '' && $this->acl->rules->media_places_flickr ){ ?>
						,
						{
							name: "Flickr",
							title: "<?php echo JText::_( 'COM_EASYBLOG_MM_FLICKR' );?>",
							options: {
								associated: <?php echo $flickrAssociated ? 'true' : 'false'; ?>,
								id: "flickr"
							}
						}
						<?php } ?>

						<?php if( $system->config->get( 'integrations_jomsocial_album' ) && JFile::exists( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' ) && $this->acl->rules->media_places_album ){ ?>
						,
						{
							name: "Jomsocial",
							title: "<?php echo JText::_( 'COM_EASYBLOG_MM_MY_ALBUMS' );?>",
							options: {
								id: "jomsocial"
							}
						}
						<?php } ?>
					],

					controller: {
						dashboard: window.parent.EasyBlog.dashboard
					}

				}, function() {

					window.___MediaManager = this;
				});
	});
</script>

<div id="MediaManager">
    <div class="placeMenuGroup"></div>
    <div class="placeBodyGroup"></div>
    <div class="recentActivities">
    	<div class="recentHeader"><?php echo JText::_( 'COM_EASYBLOG_MM_RECENT_INSERTS' );?></div>
    	<div class="recentContent">
    			<div class="recentItemGroup"></div>
    	</div>
    </div>
</div>
