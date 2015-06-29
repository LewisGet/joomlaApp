<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyBlog(function($) {

	window.purgeHistory = function(){
		if( !confirm('<?php echo JText::_( 'COM_EASYBLOG_CONFIRM_PURGE_HISTORY' );?>' ) )
		{
			return false;
		}
		$( '#purgeForm' ).submit();
	}

	<?php if( EasyBlogHelper::getJoomlaVersion() >= 1.6 ){ ?>
		Joomla.submitbutton = function( action ) {
			purgeHistory();
	    }
	<?php } else { ?>
	window.submitbutton = function( action )
	{
		purgeHistory();
	}
	<?php } ?>
});
</script>
<form id="purgeForm" method="post">
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="boxchecked" value="1" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="task" value="purge" />
<input type="hidden" name="c" value="migrators" />
</form>
<div id="config-document">
	<div id="page-joomla" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('joomla');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-smartblog" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('smartblog');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-lyften" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('lyften');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-myblog" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('myblog');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-wordpress" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('wordpress');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-wordpressimport" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('wordpressimport');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-k2" class="tab">
		<div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('k2');?></td>
				</tr>
			</table>
		</div>
	</div>
	
	<!-- div id="page-jomcomment">
		<table class="noshow">
			<tr>
				<td><?php echo $this->loadTemplate('jomcomment');?></td>
			</tr>
		</table>
	</div>
	<div id="page-jcomments">
		<table class="noshow">
			<tr>
				<td><?php echo $this->loadTemplate('jcomments');?></td>
			</tr>
		</table>
	</div>
	<div id="page-mxcomments">
		<table class="noshow">
			<tr>
				<td><?php echo $this->loadTemplate('mxcomments');?></td>
			</tr>
		</table>
	</div -->
</div>
<div class="clr"></div>