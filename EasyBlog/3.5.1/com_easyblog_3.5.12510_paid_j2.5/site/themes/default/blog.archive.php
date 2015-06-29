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
?>
<div id="ezblog-body">
	<div id="ezblog-label" class="latest-post clearfix">
		<span><?php echo $archiveTitle; ?></span>
		<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=archive' );?>" class="float-r"><?php echo JText::_( 'COM_EASYBLOG_SWITCH_TO_LIST_VIEW' ); ?></a>
	</div>

	<ul class="archive-list reset-ul">
	<?php
	if( $data )
	{
		foreach ( $data as $row )
		{
			$isMineBlog = EasyBlogHelper::isMineBlog($row->created_by, $my->id);

			$team   = '';
			if( isset($row->team_id) )
			{
			$team	= ( !empty( $row->team_id )) ? $row->team_id : '';
			}

			$blogger    = EasyBlogHelper::getTable( 'Profile', 'Table');
			$blogger->load( $row->created_by );
	?>
	<li id="entry-<?php echo $row->id; ?>" class="post-wrapper<?php echo !empty( $row->source ) ? ' micro-' . $row->source : ' micro-post';?>">
		<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $row->created ); ?>">
			<?php echo $this->formatDate( $system->config->get('layout_dateformat') , $row->created ); ?>
		</time>
		<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id); ?>"><?php echo $row->title; ?></a>
		<?php if( $row->isFeatured ) { ?><b class="tag-featured"><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURED'); ?></b><?php } ?>
	</li>
		<?php } ?>
	<?php } else { ?>
		<li>
			<div class="eblog-message info"><?php echo $emptyPostMsg;?></div>
		</li>
	<?php } ?>
	</ul>
    <?php if ( $pagination ) : ?>
    <div class="pagination clearfix">
        <?php echo $pagination; ?>
    </div>
    <?php endif; ?>
</div>
