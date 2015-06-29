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

// Localized variables
$isMine			= $row->created_by == $system->my->id;
$isTeamAdmin    = false;

if( isset( $row->team_id ) )
{
	$teamBlog   = EasyBlogHelper::getTable( 'TeamBlog', 'Table');
	$teamBlog->load( $row->team_id );

	$isTeamAdmin    = $teamBlog->isTeamAdmin( $system->my->id );
}
?>
<?php if( $system->admin || $isMine || ( $isMine && $this->acl->rules->delete_entry ) || $this->acl->rules->feature_entry ){ ?>
<div class="blog-admin pabs">
	<a href="javascript:void(0);" class="ir">#</a>
	<ul class="admin-option reset-ul admin_menu">
		<?php if( $this->acl->rules->feature_entry ){ ?>
		<li class="featured_add" <?php echo ($row->isFeatured) ? 'style="display:none;"' : '';?> >
			<a href="javascript:eblog.featured.add('post','<?php echo $row->id;?>');">
				<?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_THIS'); ?>
			</a>
		</li>
		<li class="featured_remove" <?php echo ($row->isFeatured) ? '' : 'style="display:none;"';?> >
			<a href="javascript:eblog.featured.remove('post','<?php echo $row->id;?>');">
				<?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_REMOVE'); ?>
			</a>
		</li>
		<?php } ?>

		<?php if($system->admin || $isTeamAdmin || $this->acl->rules->moderate_entry || $isMine ){ ?>
		<li class="edit">
			<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=write&blogid='.$row->id);?><?php echo ($system->config->get( 'layout_dashboardanchor' ) ) ? '#write-entry' : '';?>">
				<?php echo Jtext::_('COM_EASYBLOG_ADMIN_EDIT_ENTRY'); ?>
			</a>
		</li>
		<?php } ?>

		<?php if($system->admin || ($isMine && $this->acl->rules->delete_entry)){ ?>
		<li class="delete">
			<a href="javascript:eblog.blog.confirmDelete( '<?php echo $row->id;?>' , '<?php echo $currentURL;?>' );"><?php echo Jtext::_('COM_EASYBLOG_ADMIN_DELETE_ENTRY'); ?></a>
		</li>
		<?php } ?>

		<?php if($row->published == '1' && ($system->admin || ($isMine && $this->acl->rules->publish_entry))) { ?>
		<li class="unpublish">
			<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&controller=dashboard&from=eblog&task=toggleBlogStatus&status=0&blogId=' . $row->id );?>"><?php echo Jtext::_('COM_EASYBLOG_ADMIN_UNPUBLISH_ENTRY'); ?></a>
		</li>
		<?php } ?>
	</ul>
</div>
<?php } ?>
