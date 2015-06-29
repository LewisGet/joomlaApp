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

if( !$useImageManager )
{
	return;
}

$jCfg = JFactory::getConfig();
$joomlaDebug = $jCfg->getValue('debug');
?>

<script type="text/javascript">
EasyBlog.require()
.script(
	"dashboard/media",
	"dashboard/media.mini.launcher"
)
.done(function($){

	EasyBlog.dashboard.element
		.implement(
			EasyBlog.Controller.Dashboard.Media
		);

	$(".ui-togmenu.miniManager")
		.implement(
			EasyBlog.Controller.Dashboard.Media.Mini.Launcher,
			{
				container: ".ui-togbox.miniManager",

				manager: {

					showMediaManagerButton: true,

					places: [
						{
							id: "user:<?php echo $system->my->id; ?>",
							title: "<?php echo JText::_( 'COM_EASYBLOG_MM_MY_MEDIA' , true );?>"
						}
						<?php if( $system->config->get( 'main_media_manager_place_shared_media' ) && isset($this->acl->rules->media_places_shared) && $this->acl->rules->media_places_shared ){ ?>
						,
						{
							id: "shared",
							title: "<?php echo JText::_( 'COM_EASYBLOG_MM_SHARED_MEDIA' );?>"
						}
						<?php } ?>
					]

					,threadLimit: <?php echo ($joomlaDebug) ? 1 : 8; ?>

					,useLightbox: <?php echo $system->config->get( 'main_media_manager_image_panel_enable_lightbox' ) ? 'true' : 'false'; ?>

					,enforceImageDimension: <?php echo ( $system->config->get( 'main_media_manager_image_panel_enforce_image_dimension' ) ) ? 'true' : 'false'; ?>

					,enforceImageWidth: <?php echo $system->config->get( 'main_media_manager_image_panel_enforce_image_width' , 400 ); ?>

					,enforceImageHeight: <?php echo $system->config->get( 'main_media_manager_image_panel_enforce_image_height' , 400 ); ?>

					<?php if( $this->acl->rules->upload_image && $system->config->get( 'main_media_mini_manager_upload' ) ){ ?>
					,
					uploader: {
						place: 'user:<?php echo $system->my->id; ?>',
						url: '<?php echo JURI::base(); ?>index.php?option=com_easyblog&lang=en&controller=media&task=upload&tmpl=component&format=json&sessionid=<?php echo JFactory::getSession()->getId(); ?>&<?php echo JUtility::getToken();?>=1&place=user:<?php echo $system->my->id; ?>',
						max_file_size: '<?php echo $system->config->get( 'main_upload_image_size' );?>mb',
						filters: [{title: "Image files", extensions: "jpg,png,gif"}]
					}
					<?php } ?>
				}
			}
		);

});
</script>

<a href="javascript:void(0);" class="ico-dimage float-l prel mrs ui-togmenu miniManager" togbox="miniManager">
	<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_IMAGES' );?></b>
	<span class="ui-toolnote">
		<i></i>
		<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_IMAGES' );?></b>
		<span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_INSERT_MEDIA_TIPS'); ?></span>
	</span>
</a>
