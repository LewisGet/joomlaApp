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

<fieldset class="eblog_login" style="border: solid 1px #cccccc; padding: 10px;">
	<h3><?php echo JText::_('COM_EASYBLOG_RESTRICTED_ACCESS_TITLE');?></h3>
	<p><?php echo $message; ?></p>
	
	<p><a href="javascript:void(0);" onclick="history.back();"><?php echo JText::_('BACK');?></a></p>
</fieldset>