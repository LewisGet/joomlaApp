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
<div style="line-height: 16px;">
	<?php echo JText::_( 'COM_EASYBLOG_AUP_MEDALS_AWARED' ); ?>:
	<?php foreach( $medals as $medal ){ ?>
		<img src="<?php echo JURI::root();?>components/com_alphauserpoints/assets/images/awards/icons/<?php echo $medal->icon;?>" title="<?php echo $medal->rank;?>" />
	<?php } ?>
	<?php if( count($medals) <= 0 ) { echo JText::_('COM_EASYBLOG_AUP_NO_MEDALS'); } ?>
</div>
