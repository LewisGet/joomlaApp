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
<p><?php echo $message; ?></p>
<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_CLOSE_BUTTON' );?>" class="button" id="edialog-submit" name="edialog-submit" onclick="ejax.closedlg();" />
	<span id="eblog_loader"></span>
</div>