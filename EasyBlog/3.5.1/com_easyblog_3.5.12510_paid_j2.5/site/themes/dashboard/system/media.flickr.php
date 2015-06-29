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
<div class="flickr-wrap">
	<div>
		<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&controller=oauth&task=revoke&type=' . EBLOG_OAUTH_FLICKR . '&redirect=' . $redirect );?>"><?php echo JText::_( 'Revoke your Flickr access' ); ?></a>
	</div>
	<ul>
		<?php if( $photos ){ ?>
			<?php foreach( $photos as $photo ){ ?>
				<span><?php echo $photo->title;?></span>
				<img src="<?php echo $photo->thumbnail;?>" title="<?php echo $this->escape( $photo->title );?>" />
			<?php } ?>
		<?php } ?>
	</ul>
</div>