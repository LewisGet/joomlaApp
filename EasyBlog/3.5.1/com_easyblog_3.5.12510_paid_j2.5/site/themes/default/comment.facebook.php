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
<div id="section-comments" class="blog-section">
	<h3 class="section-title"><span><?php echo JText::_('COM_EASYBLOG_COMMENTS'); ?></span></h3>
	<div id="fb-root"></div>
	<script src="http://connect.facebook.net/<?php echo $language[0];?>_<?php echo JString::strtoupper( $language[1] );?>/all.js#xfbml=1"></script>
	<fb:comments href="<?php echo EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id , false , true );?>" num_posts="10" width="auto"></fb:comments>
</div>