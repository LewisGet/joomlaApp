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

<style type="text/css">
body {
	background:#ddd;
}
.flickrLogin{
    font-size:20px;
    width:620px;
    margin:20px auto 0;
    text-align: center;
}
#flickr-login img {
	cursor: pointer !important;
}

</style>

<div class="flickrLogin">
	<p><?php echo JText::_( 'COM_EASYBLOG_MEDIA_FLICKR_ACCESS'); ?></p>

	<a id="flickr-login">
		<img src="<?php echo JURI::root();?>components/com_easyblog/assets/images/oauth/yahoo.png" border="0" />
	</a>
</div>

<script type="text/javascript">

EasyBlog.ready(function($){

	$( '#flickr-login' ).bind( 'click' , function(){
		var url = '<?php echo rtrim( JURI::root() , '/' );?>/index.php?option=com_easyblog&controller=oauth&task=request&type=<?php echo EBLOG_OAUTH_FLICKR;?>&tmpl=component&redirect=<?php echo $redirect;?>';
		window.open(url, "Yahoo! Flickr Login", 'scrollbars=no,resizable=no, width=650,height=700');
	});
});

function showResult()
{
	// Activate the place.
	top.document.window.__flickr.activate();
	
	// Remove the login form from the parent window since this page shouldn't be seen anymore.
	top.document.window.__flickr.loginForm.remove();


}
</script>
