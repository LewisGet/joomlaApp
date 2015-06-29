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
<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6'){ ?>
Joomla.submitbutton = function( action )
{
	submitbutton(action);
}
<?php } ?>

function submitbutton( action )
{
	if(action == 'enableall')
	{
		checkOptions( 'enable' );
	}
	else if(action == 'disableall')
	{
		checkOptions( 'disable' );
	}
	else
	{
		submitform(action);
	}
}

EasyBlog.ready(function($){
	checkOptions = function( type )
	{
		if( type == 'enable' )
		{
			$( '#theme-params .option-enable' ).trigger( 'click' );
		}
		else
		{
			$( '#theme-params .option-disable' ).trigger( 'click' );
		}
	}
});
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<table width="100%">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_THEME_INFO' ); ?></legend>

				<table width="100%" cellspacing="1" class="paramlist admintable">
					<tbody>
						<tr class="">
							<td width="10%" class="key">
								<span><?php echo JText::_( 'COM_EASYBLOG_THEME_NAME');?></span>
							</td>
							<td class="paramlist_value">
								<div class="pt-5"><strong><?php echo $this->theme->name;?></strong><?php echo $this->config->get( 'layout_theme' ) == $this->theme->name ? ' (default)' : ''; ?></div>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip"><?php echo JText::_( 'COM_EASYBLOG_THEME_PATH' );?></span>
							</td>
							<td class="paramlist_value">
								<input type="text" value="<?php echo $this->theme->path; ?>" disabled="disabled" class="inputbox full-width"/>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip"><?php echo JText::_( 'COM_EASYBLOG_THEME_DESCRIPTION');?></span>
							</td>
							<td class="paramlist_value">
								<div class="pt-5"><?php echo $this->theme->desc;?></div>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip"><?php echo JText::_( 'COM_EASYBLOG_PREVIEW');?></span>
							</td>
							<td class="paramlist_value">
								<img src="<?php echo JURI::root();?>components/com_easyblog/themes/<?php echo $this->theme->name;?>/preview.png" style="border: 1px solid #ccc;"/>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>

			<?php if( $this->blogImage ){ ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_FRONTPAGE' ); ?></legend>
				<p class="small"><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_FRONTPAGE_DESC' ); ?></p>

				<table width="100%" cellspacing="1" class="paramlist admintable">
					<tbody>
						<tr>
							<td width="10%" class="key">
								<span><?php echo JText::_( 'COM_EASYBLOG_THEME_SHOW_BLOG_IMAGE_FRONTPAGE');?></span>
							</td>
							<td class="paramlist_value">
								<?php echo $this->renderCheckbox( 'params[blogimage_frontpage]' , $this->param->get( 'blogimage_frontpage') ); ?>
							</td>
						</tr>
						<tr>
							<td width="10%" class="key">
								<span><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_WIDTH');?></span>
							</td>
							<td class="paramlist_value">
								<input class="inputbox" style="width: 35px;" name="blogimage_frontpage_width" value="<?php echo $this->blogImage['frontpage']->width;?>" /> <?php echo JText::_( 'COM_EASYBLOG_PIXELS' ); ?>
							</td>
						</tr>
						<tr>
							<td width="10%" class="key">
								<span><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_HEIGHT');?></span>
							</td>
							<td class="paramlist_value">
								<input class="inputbox" style="width: 35px;" name="blogimage_frontpage_height" value="<?php echo $this->blogImage['frontpage']->height;?>" /> <?php echo JText::_( 'COM_EASYBLOG_PIXELS' ); ?>
							</td>
						</tr>
						<tr>
							<td width="10%" class="key">
								<span><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_METHOD');?></span>
							</td>
							<td class="paramlist_value">
								<select class="inputbox" name="blogimage_frontpage_resize">
									<option value="within"<?php echo $this->blogImage['frontpage']->resize == 'within' ? ' selected="selected"' :'';?>><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_WITHIN' ); ?></option>
									<option value="fit"<?php echo $this->blogImage['frontpage']->resize == 'fit' ? ' selected="selected"' :'';?>><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_FIT' ); ?></option>
									<option value="fill"<?php echo $this->blogImage['frontpage']->resize == 'fill' ? ' selected="selected"' :'';?>><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_FILL' ); ?></option>
									<option value="crop"<?php echo $this->blogImage['frontpage']->resize == 'crop' ? ' selected="selected"' :'';?>><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_CROP' ); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>

			
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_ENTRYPAGE' ); ?></legend>
				<p class="small"><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_ENTRYPAGE_DESC' ); ?></p>


				<table width="100%" cellspacing="1" class="paramlist admintable">
					<tbody>
						<tr>
							<td width="10%" class="key">
								<span><?php echo JText::_( 'COM_EASYBLOG_THEME_SHOW_BLOG_IMAGE_ENTRY');?></span>
							</td>
							<td class="paramlist_value">
								<?php echo $this->renderCheckbox( 'params[blogimage_entry]' , $this->param->get( 'blogimage_entry') ); ?>
							</td>
						</tr>
						<tr>
							<td width="10%" class="key">
								<span><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_WIDTH');?></span>
							</td>
							<td class="paramlist_value">
								<input class="inputbox" style="width: 35px;" name="blogimage_entry_width" value="<?php echo $this->blogImage[ 'entry' ]->width;?>" /> <?php echo JText::_( 'COM_EASYBLOG_PIXELS' ); ?>
							</td>
						</tr>
						<tr>
							<td width="10%" class="key">
								<span><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_HEIGHT');?></span>
							</td>
							<td class="paramlist_value">
								<input class="inputbox" style="width: 35px;" name="blogimage_entry_height" value="<?php echo $this->blogImage['entry']->height;?>" /> <?php echo JText::_( 'COM_EASYBLOG_PIXELS' ); ?>
							</td>
						</tr>
						<tr>
							<td width="10%" class="key">
								<span><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_METHOD');?></span>
							</td>
							<td class="paramlist_value">
								<select class="inputbox" name="blogimage_entry_resize">
									<option value="within"<?php echo $this->blogImage['entry']->resize == 'within' ? ' selected="selected"' :'';?>><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_WITHIN' ); ?></option>
									<option value="fit"<?php echo $this->blogImage['entry']->resize == 'fit' ? ' selected="selected"' :'';?>><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_FIT' ); ?></option>
									<option value="fill"<?php echo $this->blogImage['entry']->resize == 'fill' ? ' selected="selected"' :'';?>><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_FILL' ); ?></option>
									<option value="crop"<?php echo $this->blogImage['entry']->resize == 'crop' ? ' selected="selected"' :'';?>><?php echo JText::_( 'COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_CROP' ); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			<?php } ?>

		</td>
		<td width="50%" valign="top">
			<fieldset class="adminform theme-param" id="theme-params">
				<legend><?php echo JText::_( 'COM_EASYBLOG_THEME_PARAMETERS' ); ?></legend>
				<table width="100%" cellspacing="1" class="paramlist admintable">
				<tbody>
					<?php echo $this->renderParams( $this->theme->name ); ?>
				</tbody>
				</table>
			</fieldset>
		</td>
	</tr>
</table>
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="c" value="themes" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="element" value="<?php echo $this->theme->name; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
