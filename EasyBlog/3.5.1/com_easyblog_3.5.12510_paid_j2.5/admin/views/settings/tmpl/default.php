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

EasyBlog(function($){

	<?php
	if($this->joomlaversion >= '1.6')
	{
	?>

	Joomla.submitbutton = function(task){
		$('#submenu li').children().each( function(){
			if( $(this).hasClass( 'active' ) )
			{
				$( '#active' ).val( $(this).attr('id') );
			}
		});

		$('dl#subtabs').children().each( function(){
			if( $(this).hasClass( 'open' ) )
			{
				$( '#activechild' ).val( $(this).attr('class').split(" ")[0] );
			}

		});

		Joomla.submitform(task);
	}

	<?php
	}
	else
	{
	?>

	window.submitbutton = function( action )
	{
		$('#submenu li').children().each( function(){
			if( $(this).hasClass( 'active' ) )
			{
				$( '#active' ).val( $(this).attr('id') );
			}
		});

		$('dl#subtabs').children().each( function(){
			if( $(this).hasClass( 'open' ) )
			{
				$( '#activechild' ).val( $(this).attr('id') );
			}
		});

		submitform( action );
	}

	<?php
	}
	?>

	window.switchFBPosition = function()
	{
		if( $('#main_facebook_like_position').val() == '1' )
		{
		    $('#fb-likes-standard').hide();
		    if( $('#standard').attr('checked') == true)
		    	$('#button_count').attr('checked', true);
		}
		else
		{
		    $('#fb-likes-standard').show();
		}
	}

});

</script>
<?php
// There seems to be some errors when suhosin is configured with the following settings
// which most hosting provider does! :(
//
// suhosin.post.max_vars = 200
// suhosin.request.max_vars = 200
if(in_array('suhosin', get_loaded_extensions()))
{
	$max_post		= @ini_get( 'suhosin.post.max_vars');
	$max_request	= @ini_get( 'suhosin.request.max_vars' );

	if( !empty( $max_post ) && $max_post < 400 || !empty( $max_request ) && $max_request < 400 )
	{
?>
	<div class="error" style="background: #ffcccc;border: 1px solid #cc3333;padding: 5px;">
		<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SUHOSIN_CONFLICTS' );?>
		<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SUHOSIN_CONFLICTS_MAX' );?>
		<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SUHOSIN_RESOLVE_MESSAGE' ); ?>
	</div>
<?php
	}
}
?>
<form action="index.php" method="post" name="adminForm" id="settingsForm">
<div id="config-document">
	<div id="page-main" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('main');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-ebloglayout" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('layout');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-media" class="tab">
		<div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('media');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-seo" class="tab">
		<div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('seo');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-comments" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('comments');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-integrations" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('integrations');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-notifications" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('notifications');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-social" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('social');?></td>
				</tr>
			</table>
		</div>
	</div>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="active" id="active" value="" />
<input type="hidden" name="activechild" id="activechild" value="" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="c" value="settings" />
</form>
