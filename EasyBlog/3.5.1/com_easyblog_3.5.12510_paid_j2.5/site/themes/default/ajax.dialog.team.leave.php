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
<form name="frmLeave" id="frmLeave">
<div class="clearfix">
	<img class="float-l avatar mrm" src="<?php echo $team->getAvatar();?>" width="60" height="60" />
	<div><?php echo JText::sprintf('COM_EASYBLOG_TEAMBLOG_LEAVE_REQUEST_DESC', $team->title); ?></div>
</div>
<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_CLOSE_BUTTON');?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_PROCEED_BUTTON');?>" class="button" id="edialog-submit" name="edialog-submit" onclick="eblog.teamblog.leaveteam();" />
	<input class="inputbox" type="hidden" name="id" value="<?php echo $team->id; ?>" />
	<input class="inputbox" type="hidden" name="userid" value="<?php echo $system->my->id; ?>" />
	<span id="eblog_loader"></span>
</div>