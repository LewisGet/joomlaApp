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
<div id="section-comments">
	<!-- START: Livefyre Embed -->
	<script type='text/javascript' src='http://zor.livefyre.com/wjs/v1.0/javascripts/livefyre_init.js'></script>
	<script type='text/javascript'>
		var fyre = LF({
			site_id: <?php echo $siteId;?>,
			article_id: "<?php echo $blog->id;?>",
			article_title: "<?php echo $blog->title;?>"
		});
	</script>
	<!-- END: Livefyre Embed -->
</div>		
