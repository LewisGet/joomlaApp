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
<script type="text/javascript">
EasyBlog.require()
	.script(
		'tag'
		)
	.view( 'dashboard/dashboard.tags.item' )
	.done( function($){
		$("<?php echo $microblogType ?> .tag-form")
			.implement(
				"EasyBlog.Controller.Tag.Form",
				{
					tags: '',
					tagSelections: <?php echo $this->json_encode($tags); ?>,
					views: {
						"tagItem"	: 'dashboard/dashboard.tags.item'
					}
				},
				function(){}
		);
	});
</script>
<div class="tag-form">
	<div class="write-taglist">
		<ul class="tag-list creation reset-ul float-li clearfix">
			<?php if($this->acl->rules->create_tag): ?>
			<li class="new-tag-item">
				<input type="text" name="tag-input" class="tag-input" autocomplete="off"/>
				<button type="button" class="tag-create"><?php echo JText::_('COM_EASYBLOG_ADD_TAG'); ?></button>
			</li>
			<?php endif; ?>
		</ul>
	</div>

	<ul class="tag-list selection reset-ul float-li clearfix">
		<li class="more-tags"><?php echo JText::_('COM_EASYBLOG_TAG_MORE'); ?> &raquo;</li>
	</ul>
</div>
