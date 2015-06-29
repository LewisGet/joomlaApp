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
	.script( 'dashboard/blogimage' )
	.done(function($){

		window.blogImageLoaded 	= false;

		$( 'h3#blog-image' ).bind( 'click' ,function(){

			$(".write-blogimage")
				.implement(
					EasyBlog.Controller.Dashboard.BlogImage,
					{
						resizeUsing: "resizeWithin",

						manager: {

							showMediaManagerButton: false,

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

							<?php if( $this->acl->rules->upload_image ){ ?>
							,
							uploader: {
								place: "user:<?php echo $this->my->id; ?>",
								url: '<?php echo JURI::base(); ?>index.php?option=com_easyblog&controller=media&task=upload&tmpl=component&format=json&sessionid=<?php echo JFactory::getSession()->getId(); ?>&<?php echo JUtility::getToken();?>=1&place=user:<?php echo $this->my->id; ?>',
								max_file_size: '<?php echo $this->config->get( 'main_upload_image_size' );?>mb',
								filters: [{title: "Image files", extensions: "jpg,png,gif"}]
							}
							<?php } ?>
						}
					}, function(){
						<?php if( $this->blog->image ){ ?>

						if( !window.blogImageLoaded )
						{
							var item = {
								meta: <?php echo $this->blog->image;?>
							};

							this.insertImage( item );
						}

						window.blogImageLoaded	= true;
						<?php } ?>
					}
				);
		});
	});


</script>
<div class="write-blogimage">
    <div class="mbl"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_BLOG_IMAGE_DESC' ); ?></div>

	<div class="blogImagePlaceHolder">
		<input type="hidden" name="image" value='<?php echo $this->blog->image;?>' />
		<div class="imagePlaceHolder"></div>
	</div>

	<div class="blogImageControl clearfix">
		<a href="javascript:void(0);" class="selectBlogImage buttons"><?php echo JText::_( 'COM_EASYBLOG_SELECT_BLOG_IMAGE' );?></a>
		<a href="javascript:void(0);" class="removeBlogImage buttons float-r"><?php echo JText::_( 'COM_EASYBLOG_REMOVE_BLOG_IMAGE' );?></a>
	</div>

    <div class="blogImageDock miniManager"></div>
</div>
