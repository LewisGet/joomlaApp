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
		.script("dashboard/medialink")
		.done(function($) {
			$(".ui-medialink").implement(EasyBlog.Controller.Dashboard.MediaLink);
		});
</script>

<?php if( $system->config->get( 'layout_dashboard_zemanta' ) ) { ?>
<script type="text/javascript">
	EasyBlog.require()
		.script(
			'http://static.zemanta.com/core/jquery.js',
			'http://static.zemanta.com/core/jquery.zemanta.js'
		)
		.stylesheet(
			'http://static.zemanta.com/core/zemanta-widget.css'
		)
		.done(function($){
		});
</script>
<?php } ?>

<script type="text/javascript" src="<?php echo JURI::root();?>components/com_easyblog/assets/vendors/clipboard/ZeroClipboard.js"></script>

<div id="editor-content" class="clearfix">

	<div class="ui-medialink">

		<div class="ui-togmenugroup clearfix pas">
			<select id="published" name="published" class="input select float-r">
				<option value="1"<?php echo $blog->published ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_PUBLISHED');?></option>
				<option value="0"<?php echo !$blog->published ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_UNPUBLISHED');?></option>
			</select>

			<a href="javascript:void(0);" class="ico-dglobe float-l prel mrs ui-togmenu olderPosts" togbox="olderPosts">
				<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_EDITOR_OLDER_POSTS'); ?></b>
				<span class="ui-toolnote">
					<i></i>
					<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_EDITOR_OLDER_POSTS'); ?></b>
					<span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_EDITOR_INSERT_LINK_ADD_TO_CONTENT_TIPS'); ?></span>
				</span>
			</a>
			<i></i>
	        <?php echo $this->fetch( 'dashboard.write.images.php' ); ?>
	        <i></i>
			<?php echo $this->fetch( 'dashboard.write.videos.php' ); ?>

			<?php if( $system->config->get( 'layout_dashboard_zemanta' ) ) { ?>
			<i></i>
			<a href="javascript:void(0);" class="ico-dzemanta float-l prel ui-togmenu zemantaButton" togbox="zemantaPanel">
				<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_ZEMANTA' ); ?></b>
				<span class="ui-toolnote">
					<i></i>
					<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_EDITOR_INSERT_ZEMANTA' ); ?></b>
					<span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_EDITOR_INSERT_ZEMANTA_TIPS'); ?></span>
				</span>
				</a>
			<?php } ?>
		</div>

		<div class="ui-togbox olderPosts">
			<div class="pas search-field" style="background:#f5f5f5;">
	            <div class="pas mrl">
					<input type="text" id="search-content" class="input width-half" onblur="if (this.value == '') {this.value = '<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_PREVIOUS_POST'); ?>';}" onfocus="if (this.value == '<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_PREVIOUS_POST'); ?>') {this.value = '';}" value="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_PREVIOUS_POST'); ?>" />
					<input type="button" onclick="eblog.editor.search.load();return false;" value="<?php echo JText::_('COM_EASYBLOG_SEARCH'); ?>" class="buttons mls" />
				</div>
			</div>
			<div class="search-results-content"></div>
		</div>

		<div class="ui-togbox miniManager"></div>

		<?php if( $system->config->get( 'layout_dashboard_zemanta' ) ){ ?>
		<div class="ui-togbox zemantaPanel">
			<div id="zemanta-sidebar">
				<div id="zemanta-control" class="zemanta"></div><div id="zemanta-message" class="zemanta">Loading Zemanta...</div><div id="zemanta-filter" class="zemanta"></div><div id="zemanta-gallery" class="zemanta"></div><div id="zemanta-articles" class="zemanta"></div><div id="zemanta-preferences" class="zemanta"></div>
			</div>

			<div id="zemanta-links">
				<ul id="zemanta-links-div-ul">
					<li class="zemanta-title"><?php echo JText::_( 'COM_EASYBLOG_ZEMANTA_LINK_RECOMMENDATIONS' );?></li>
				</ul>
				<p class="zem-clear">&nbsp;</p>
			</div>
		</div>
		<script type="text/javascript">
		window.ZemantaGetAPIKey = function () {
			return '<?php echo $system->config->get( 'layout_dashboard_zemanta_api' );?>';
		}
		</script>
		<script type="text/javascript" src="<?php echo JURI::root();?>components/com_easyblog/assets/js/zemanta.platform.js"></script>
		<?php } ?>
	</div>
</div>
