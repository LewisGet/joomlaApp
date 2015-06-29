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
<p><?php echo JText::_( 'COM_EASYBLOG_ARE_YOU_SURE_YOU_WANT_TO_UNSUBSCRIBE_POST' );?></p>
<form id="dashboard" name="dashboard" method="post" action="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&controller=entry&task=unsubscribe' );?>">
	<input type="hidden" name="subscription_id" value="<?php echo $subscription_id; ?>" />
	<input type="hidden" name="blog_id" value="<?php echo $blog_id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>

	<div class="dialog-actions">
		<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' );?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
		<input type="submit" value="<?php echo JText::_( 'COM_EASYBLOG_PROCEED_BUTTON' );?>" class="button" />
	</div>
</form>
