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
<div>
	<ul>
		<?php if( $albums ){ ?>
			<?php foreach( $albums as $album ){ ?>
				<span><?php echo $album->name;?></span>
				<img src="<?php echo $album->thumbnail;?>" title="<?php echo $this->escape( $album->name );?>" width="64" />
			<?php } ?>
		<?php } ?>
	</ul>
</div>