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

$jCfg = JFactory::getConfig();
$joomlaDebug = $jCfg->getValue('debug');
?>
<script type="text/javascript">
EasyBlog
	.require()
	.script(
		"dashboard/media",
		"dashboard/media.mini.launcher"
	)
	.done(function($){

		EasyBlog.dashboard.element
			.implement(EasyBlog.Controller.Dashboard.Media);

		$(".ui-togmenu.miniManager")
		.implement(
			EasyBlog.Controller.Dashboard.Media.Mini.Launcher,
			{
				container: ".ui-togbox.miniManager",

				manager: {

					showMediaManagerButton: true,

					places: [
						{
							id: "user:<?php echo $this->my->id; ?>",
							title: "<?php echo JText::_( 'COM_EASYBLOG_MM_MY_MEDIA' , true );?>"
						},
						{
							id: "shared",
							title: "<?php echo JText::_( 'COM_EASYBLOG_MM_SHARED_MEDIA' );?>"
						}
					]

					,threadLimit: <?php echo ($joomlaDebug) ? 1 : 8; ?>

					,useLightbox: <?php echo $this->config->get( 'main_media_manager_image_panel_enable_lightbox' ) ? 'true' : 'false'; ?>

					,enforceImageDimension: <?php echo ( $this->config->get( 'main_media_manager_image_panel_enforce_image_dimension' ) ) ? 'true' : 'false'; ?>

					,enforceImageWidth: <?php echo $this->config->get( 'main_media_manager_image_panel_enforce_image_width' , 400 ); ?>

					,enforceImageHeight: <?php echo $this->config->get( 'main_media_manager_image_panel_enforce_image_height' , 400 ); ?>

					,
					uploader: {
						place: "user:<?php echo $this->my->id; ?>",
						url: '<?php echo JURI::base(); ?>index.php?option=com_easyblog&lang=en&controller=media&task=upload&tmpl=component&format=json&sessionid=<?php echo JFactory::getSession()->getId(); ?>&<?php echo JUtility::getToken();?>=1&place=user:<?php echo $this->my->id; ?>',
						max_file_size: '<?php echo $this->config->get( 'main_upload_image_size' );?>mb',
						filters: [{title: "Image files", extensions: "jpg,png,gif"}]
					}
				}
			}
		);
	});
</script>

<a href="javascript:void(0);" class="ico-dimage float-l prel mrs ui-togmenu miniManager" togbox="miniManager">
	<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_MEDIA' );?></b>
	<span class="ui-toolnote">
		<i></i>
		<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_MEDIA' );?></b>
		<span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_INSERT_MEDIA_TIPS'); ?></span>
	</span>
</a>
