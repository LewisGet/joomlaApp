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
<form name="edit-comment" id="edit-comment" action="" method="post">
<p><?php echo JText::_( 'COM_EASYBLOG_DELETE_COMMENTS_TIPS' ); ?></p>
<input class="inputbox" type="hidden" name="commentId" value="<?php echo $comment->id; ?>" />
<input class="inputbox" type="hidden" name="controller" value="entry" />
<input class="inputbox" type="hidden" name="task" value="deleteComment" />
<?php echo JHTML::_( 'form.token' ); ?>
<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON');?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="submit" value="<?php echo JText::_('COM_EASYBLOG_PROCEED_BUTTON');?>" class="button" id="edialog-submit" name="edialog-submit" />
</div>
</form>
