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
<?php if($system->config->get( 'layout_dashboard_blogimage') ){ ?>
<script type="text/javascript">

EasyBlog.require()
.script(
	"dashboard/blogimage"
)
.done(function($){

	$(".blogImage")
		.implement(
			EasyBlog.Controller.Dashboard.BlogImage,
			{
				manager: {

					showMediaManagerButton: false,

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

					<?php if( $this->acl->rules->upload_image && $system->config->get( 'main_media_mini_manager_upload' ) ){ ?>
					,
					uploader: {
						place: 'user:<?php echo $system->my->id; ?>',
						url: '<?php echo JURI::base(); ?>index.php?option=com_easyblog&controller=media&task=upload&tmpl=component&format=json&sessionid=<?php echo JFactory::getSession()->getId(); ?>&<?php echo JUtility::getToken();?>=1&place=user:<?php echo $system->my->id; ?>',
						max_file_size: '<?php echo $system->config->get( 'main_upload_image_size' );?>mb',
						filters: [{title: "Image files", extensions: "jpg,png,gif"}]
					}
					<?php } ?>
				}
			}, function(){

				<?php if( $blog->image ){ ?>

				var item = {
					meta: <?php echo $blog->image;?>
				};

				this.insertImage( item );

				<?php } ?>
			}
		);
});
</script>

<div class="blogImage clearfix">

	<div class="blogImagePreview">
		<div class="blogImagePlaceHolder">
			<input type="hidden" name="image" value='<?php echo $blog->image;?>' />
			<div class="imagePlaceHolder"></div>
		</div>
		<div class="blogImageNote">
			<i></i>
			<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_BLOG_IMAGE_HEADING' );?></b>
			<div><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_BLOG_IMAGE_DESC' );?></div>
		</div>
		<div class="blogImageControl">
			<a href="javascript:void(0);" class="selectBlogImage buttons butt-orange float-r"><?php echo JText::_( 'COM_EASYBLOG_SELECT_BLOG_IMAGE' );?></a>
			<a href="javascript:void(0);" class="doneBlogImage buttons float-r"><?php echo JText::_( 'COM_EASYBLOG_DONE_BUTTON' );?></a>
			<a href="javascript:void(0);" class="removeBlogImage buttons float-l"><?php echo JText::_( 'COM_EASYBLOG_REMOVE_BLOG_IMAGE' );?></a>
		</div>
	</div>

    <div class="blogImageDock miniManager">
    </div>
</div>
<?php } ?>
