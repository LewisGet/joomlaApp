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

$eConfig = EasyBlogHelper::getConfig();
?>
<script type="text/javascript">

EasyBlog(function($){


	window.moveCategory = function()
	{
		ejax.load( 'blogs' , 'changeCategory' );
	}

	window.autopost = function( type , id )
	{
		ejax.load( 'blogs' , 'confirmAutopost' , type , id );
	}

	<?php if(EasyBlogHelper::getJoomlaVersion() >= 1.6) : ?>
		Joomla.submitbutton = function( action ) {

			if( action == 'showMove' )
			{
				moveCategory();

				return false;
			}

			if( action == 'moveCategory' )
			{
				$( '#adminForm input[name=move_category_id]' ).val( $('#move_category' ).val() );
			}

			if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYBLOG_ARE_YOU_SURE_CONFIRM_DELETE', true); ?>')) {
				Joomla.submitform( action );
			}
		}
	<?php else : ?>
	window.submitbutton = function( action )
	{
		if( action == 'showMove' )
		{
			moveCategory();

			return false;
		}

		if( action == 'moveCategory' )
		{
			$( '#adminForm input[name=move_category_id]' ).val( $('#move_category' ).val() );
		}

		if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYBLOG_ARE_YOU_SURE_CONFIRM_DELETE', true); ?>')) {
			submitform( action );
		}
	}
	<?php endif; ?>

});
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="adminform-head">
    <table class="adminform">
    	<tr>
    		<td width="50%">
    		  	<label><?php echo JText::_( 'COM_EASYBLOG_BLOGS_SEARCH' ); ?> :</label>
    			<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();" />
    			<button onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
    			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
    		</td>
    		<td width="50%" style="text-align:right" class="blog-filters">
                <label><?php echo JText::_( 'COM_EASYBLOG_BLOGS_FILTER_BY' ); ?> :</label>
                <?php echo $this->getFilterBlogger( $this->filteredBlogger ); ?>
    			<?php echo $this->category; ?>
    			<?php echo $this->state; ?>
				<select name="filter_source" class="inputbox" onchange="this.form.submit()">
					<option value="-1"<?php echo $this->source == '-1' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTERS_POST_TYPE');?></option>
					<option value=""<?php echo $this->source == '' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_NORMAL_POST' ); ?></option>
					<option value="link"<?php echo $this->source == 'link' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_LINK' ); ?></option>
					<option value="quote"<?php echo $this->source == 'quote' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_QUOTE' );?></option>
					<option value="photo"<?php echo $this->source == 'photo' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_PHOTO' );?></option>
					<option value="video"<?php echo $this->source == 'video' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_VIDEO' );?></option>
					<option value="twitter"<?php echo $this->source == 'twitter' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_TWITTER' );?></option>
					<option value="email"<?php echo $this->source == 'email' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_FILTER_EMAIL' );?></option>
				</select>
    			<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
				<select name="filter_language" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->filterLanguage );?>
				</select>
				<?php } ?>
    		</td>
    	</tr>
    </table>
</div>
<div class="adminform-body">
<table class="adminlist" cellspacing="1">
<thead>
	<tr>
		<th width="5"><?php echo JText::_( 'Num' ); ?></th>
		<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->blogs ); ?>);" /></th>
		<th class="title"><?php echo JHTML::_('grid.sort', 'Title', 'a.title', $this->orderDirection, $this->order ); ?></th>

		<?php if( !$this->browse ){ ?>
		<th width="10%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_CONTRIBUTED_IN' ); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_FEATURED' ); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_PUBLISHED' ); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_FRONTPAGE' ); ?></th>
		<th width="10%"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_AUTOPOSTING' ); ?></th>
		<?php } ?>

		<th width="10%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_CATEGORY' ); ?></th>
		<th width="10%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_AUTHOR' ); ?></th>

		<?php if( !$this->browse ){ ?>
		<th width="10%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_DATE', 'a.created', $this->orderDirection, $this->order ); ?></th>
		<th width="3%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_BLOGS_HITS', 'a.hits', $this->orderDirection, $this->order ); ?></th>
		<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
			<th width="5%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_LANGUAGE' );?></th>
		<?php } ?>
		<th width="20" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_PREVIEW' ); ?></th>
		<?php } ?>
	</tr>
</thead>
<tbody>
<?php
if( $this->blogs )
{
	$k = 0;
	$x = 0;
	$config	= JFactory::getConfig();
	for ($i=0, $n=count($this->blogs); $i < $n; $i++)
	{
		$row = $this->blogs[$i];
		$user		= JFactory::getUser( $row->created_by );
		$previewLink	= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $row->id, true, true);
		$preview 	= '<a href="' . $previewLink .'" target="_blank"><img src="'.JURI::base().'/images/preview_f2.png"/ style="width:20px; height:20px; "></a>';
		$editLink	= JRoute::_('index.php?option=com_easyblog&c=blogs&task=edit&blogid='.$row->id);
		$published 	= JHTML::_('grid.published', $row, $i );
		$date		= EasyBlogDateHelper::getDate($row->created);

		$extGroupName = '';
		if( !empty($row->external_group_id) )
		{
		    $blog_contribute_source = EasyBlogHelper::getHelper( 'Groups' )->getGroupSourceType();
		    $extGroupName			= EasyBlogHelper::getHelper( 'Groups' )->getGroupContribution( $row->id, $blog_contribute_source, 'name' );
		    $extGroupName           = $extGroupName . ' (' . ucfirst($blog_contribute_source) . ')';
		}

		$contributionDisplay    = '';
		if( $row->issitewide )
		{
		    $contributionDisplay    = JText::_('COM_EASYBLOG_BLOGS_WIDE');
		}
		else
		{
		    $contributionDisplay    = ( !empty( $extGroupName ) ) ? $extGroupName : $row->teamname;
		}

	?>
	<tr class="<?php echo "row$k"; ?>">
		<td style="text-align:center;">
			<?php echo $this->pagination->getRowOffset( $i ); ?>
		</td>
		<td width="7">
			<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
		</td>
		<td align="left">
			<span>
				<?php if( $this->browse ){ ?>
					<a href="javascript:void(0);" onclick="parent.<?php echo $this->browseFunction; ?>('<?php echo $row->id;?>','<?php echo $this->escape($row->title);?>');"><?php echo $row->title;?></a>
				<?php } else { ?>
					<a href="<?php echo $editLink; ?>"><?php echo $row->title; ?></a>
				<?php } ?>
			</span>
		</td>
		<?php if( !$this->browse ){ ?>
		<td align="center">
			<?php echo $contributionDisplay;  ?>
		</td>
		<td align="center">
			<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo ( EasyBlogHelper::isFeatured( EBLOG_FEATURED_BLOG , $row->id ) ) ? 'unfeature' : 'feature';?>')">
				<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/<?php echo ( EasyBlogHelper::isFeatured( EBLOG_FEATURED_BLOG , $row->id ) ) ? 'default.png' : 'nodefault.png';?>" width="16" height="16" border="0" />
			</a>
		</td>
		<td align="center">
		    <?php if($row->published == 2) : ?>
		    <img src="<?php echo JURI::base() . 'components/com_easyblog/assets/images/schedule.png';?>" border="0" alt="<?php echo JText::_('COM_EASYBLOG_SCHEDULED');?>" />
		    <?php elseif($row->published == 3) : ?>
		    <img src="<?php echo JURI::base() . 'components/com_easyblog/assets/images/draft.png';?>" border="0" alt="<?php echo JText::_('COM_EASYBLOG_DRAFT');?>" />
		    <?php elseif($row->published == POST_ID_TRASHED ) : ?>
		    <img src="<?php echo JURI::base() . 'components/com_easyblog/assets/images/trash.png';?>" border="0" alt="<?php echo JText::_('COM_EASYBLOG_TRASHED');?>" />
		    <?php else: ?>
			<?php echo $published; ?>
			<?php endif; ?>
		</td>
		<td align="center">
			<?php if( EasyBlogHelper::getJoomlaVersion() <= '1.5' ){ ?>
				<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','toggleFrontpage')"><img src="images/<?php echo ( $row->frontpage ) ? 'tick.png' : 'publish_x.png';?>" width="16" height="16" border="0" /></a>
			<?php } else { ?>
				<?php echo JHTML::_( 'grid.boolean' , $i , $row->frontpage , 'toggleFrontpage' , 'toggleFrontpage' ); ?>
			<?php } ?>
		</td>
		<td align="center">
			<?php if( $row->published && $this->centralizedConfigured ){ ?>
				<?php foreach( $this->consumers as $consumer ){
				$shared 	= $consumer->isShared( $row->id ) ? '' : '_disabled';
				$title		= empty( $shared ) ? JText::sprintf( 'COM_EASYBLOG_AUTOPOST_SHARED' , $consumer->type ) : JText::sprintf( 'COM_EASYBLOG_AUTOPOST_NOT_SHARED_YET' , $consumer->type );
				?>
				<a href="javascript:void(0);" onclick="autopost('<?php echo $consumer->type;?>','<?php echo $row->id;?>');"><img id="oauth-<?php echo $consumer->type;?>" src="<?php echo JURI::root();?>/components/com_easyblog/assets/icons/socialshare/<?php echo $consumer->type;?><?php echo $shared;?>.png" title="<?php echo $this->escape( $title );?>" /></a>
				<?php } ?>
			<?php } else { ?>
				<div class="has-tip">
					<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_NOT_AVAILABLE_BECAUSE_UNPUBLISHED' ); ?></div>
					<?php echo JText::_( 'COM_EASYBLOG_NOT_AVAILABLE' ); ?>
				</div>
			<?php } ?>
		</td>
		<?php } ?>
		<td align="center">
			<?php if( $this->browse ){ ?>
				<?php echo $this->getCategoryName( $row->category_id);?>
			<?php } else { ?>
				<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=category&task=edit&catid=' . $row->category_id);?>"><?php echo $this->getCategoryName( $row->category_id);?></a>
			<?php } ?>
		</td>
		<td align="center">
			<span class="editlinktip">
				<?php if( $this->browse ){ ?>
					<?php echo $user->name; ?>
				<?php } else { ?>
					<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo $user->name; ?></a>
				<?php } ?>
			</span>
		</td>

		<?php if( !$this->browse ){ ?>
		<td align="center">
			<?php echo EasyBlogDateHelper::toFormat($date); ?>
		</td>
		<td align="center"><?php echo $row->hits;?></td>
		<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
			<td align="center">
				<?php if ($row->language=='*' || empty( $row->language) ){ ?>
					<?php echo JText::alt('JALL', 'language'); ?>
				<?php } else { ?>
					<?php echo $this->escape( $this->getLanguageTitle( $row->language) ); ?>
				<?php } ?>
			</td>
		<?php } ?>
		<td align="center"><?php echo $row->id; ?></td>
		<td align="center" class="post-preview">
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_UNPUBLISHED_LOGIN_TO_PREVIEW' ); ?></div>
				<a href="<?php echo $previewLink;?>" target="_blank" class="preview"><?php echo JText::_('COM_EASYBLOG_PREVIEW');?></a>
			</div>
		</td>
		<?php } ?>
	</tr>
	<?php $k = 1 - $k; } ?>
<?php
}
else
{
?>
	<tr>
		<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
		<td colspan="15" align="center">
		<?php } else { ?>
		<td colspan="14" align="center">
		<?php } ?>
			<?php echo JText::_('COM_EASYBLOG_BLOGS_NO_ENTRIES');?>
		</td>
	</tr>
<?php
}
?>
</tbody>

<tfoot>
	<tr>
		<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' ){ ?>
		<td colspan="15" align="center">
		<?php } else { ?>
		<td colspan="14" align="center">
		<?php } ?>
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
</table>
</div>
<?php echo JHTML::_( 'form.token' ); ?>
<?php if( $this->browse ){ ?>
<input type="hidden" name="tmpl" value="component" />
<?php } ?>
<input type="hidden" name="autopost_type" value="" />
<input type="hidden" name="autopost_selected" value="" />
<input type="hidden" name="move_category_id" value="" />
<input type="hidden" name="browse" value="<?php echo $this->browse;?>" />
<input type="hidden" name="browseFunction" value="<?php echo $this->browseFunction;?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="view" value="blogs" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="blogs" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>
