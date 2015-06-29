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
		<span><?php echo JText::_('COM_EASYBLOG_ARCHIVE_PAGE_TITLE') ?></span>
		<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=archive&layout=calendar' );?>" class="float-r"><?php echo JText::_( 'COM_EASYBLOG_SWITCH_TO_CALENDAR_VIEW' ); ?></a>
	</div>

	<ul class="archive-list reset-ul mtl">
	<?php if( $data ){ ?>
		<?php foreach ( $data as $row ) { ?>
			<li id="entry-<?php echo $row->id; ?>" class="post-wrapper<?php echo !empty( $row->source ) ? ' micro-' . $row->source : ' micro-post';?>">
				<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $row->created ); ?>">
					<?php echo $this->formatDate( $system->config->get('layout_dateformat') , $row->created ); ?>
				</time>
				<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id); ?>"><?php echo $row->title; ?></a>
			</li>
		<?php } ?>
	<?php } else { ?>
		<li>
			<div class="eblog-message info mtm"><?php echo $emptyPostMsg;?></div>
		</li>
	<?php } ?>
    </ul>
    <div class="eblog-pagination">
    	<?php echo $pagination->getPagesLinks();?>
    </div>
</div>
