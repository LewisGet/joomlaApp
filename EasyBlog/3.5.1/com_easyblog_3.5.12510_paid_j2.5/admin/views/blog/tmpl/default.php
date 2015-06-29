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

$blog 		= $this->blog;
$draft		= $this->draft;
$editor		= $this->editor;
$acl		= $this->acl;
$author		= $this->author;

$blogId = JRequest::getInt('blogid', '');

$isPrivate = $this->isPrivate;
$allowComment = $this->allowComment;
$subscription = $this->subscription;
$frontpage = $this->frontpage;
$trackbacks = $this->trackbacks;

jimport( 'joomla.utilities.date' );
?>

<script type="text/javascript">

EasyBlog(function($) {

	<?php if(EasyBlogHelper::getJoomlaVersion() >= 1.6) : ?>
		Joomla.submitbutton = function( action ) {

		    if( action == 'rejectBlog')
		    {
				var draft_id    = $('#draft_id').val();
		        admin.blog.reject( draft_id );
				return false;
		    }
		    else
		    {
	            eblog.editor.toggleSave();
	            saveBlog();

				if( action == 'savePublishNew' )
				{
					$( '#savenew' ).val( '1' );
					action	= 'savePublish';
				}
			}
			Joomla.submitform( action );
	    }
	<?php else : ?>
	window.submitbutton = function( action )
	{
	    if( action == 'rejectBlog')
	    {
			var draft_id    = $('#draft_id').val();
	        admin.blog.reject( draft_id );
			return false;
	    }
	    else
	    {
			eblog.editor.toggleSave();
			saveBlog();

			if( action == 'savePublishNew' )
			{
				$( '#savenew' ).val( '1' );
				action	= 'savePublish';
			}
		}

		submitform( action );
	}
	<?php endif; ?>

	window.insertMember = function( id , name )
	{
		$('#authorId').val(id);
		$('#authorName').val(name);

		<?php
		if($this->joomlaversion >= '1.6')
		{
		?>
			window.parent.SqueezeBox.close();
		<?php
		}
		else
		{
		?>
			window.parent.document.getElementById('sbox-window').close();
		<?php
		}
		?>
	}

	window.insertCategory = function( id , name )
	{
		$('#category_id').val(id);
		$('#categoryTitle').val(name);

		<?php
		if($this->joomlaversion >= '1.6')
		{
		?>
			window.parent.SqueezeBox.close();
		<?php
		}
		else
		{
		?>
			window.parent.document.getElementById('sbox-window').close();
		<?php
		}
		?>
	}

	EasyBlog.ready(function($) {
		$('#title').bind('change', function() {
			eblog.editor.permalink.generate();
		});

		// Editor initialization so we can use their methods.
		eblog.editor.getContent = function(){
			<?php echo 'return ' . JFactory::getEditor( $this->config->get( 'layout_editor' ) )->getContent( 'write_content' ); ?>
		}

		eblog.editor.setContent = function( value ){
			<?php echo 'return ' . JFactory::getEditor( $this->config->get( 'layout_editor' ) )->setContent( 'write_content' , 'value' ); ?>
		}

		eblog.editor.toggleSave = function(){
			<?php echo JFactory::getEditor( $this->config->get( 'layout_editor' ) )->save( 'write_content' ); ?>
		}

		// @task: Bind the reset hits button
		$( '#reset-hits' ).bind( 'click' , function(){

			if( confirm( '<?php echo JText::_('COM_EASYBLOG_CONFIRM_RESET_HITS', true);?>' ) )
			{
				EasyBlog.ajax( 'admin.views.blog.resethits' , {
					id: '<?php echo $this->blog->id;?>'
				}, function( state ){

					if( state )
					{
						$( '.hits-counter' ).html( 0 );
					}
				});
			}
		});
	});

	$.sanitizeHTML = function(html)
	{
		var fragmentContainer = document.createElement('div'),
		fragment = document.createDocumentFragment();

		$.clean([html], document, fragment);

		fragmentContainer.appendChild(fragment);

		return fragmentContainer.innerHTML;
	}

	window.saveBlog = function()
	{
		// Retrieve the main content.
		var editorContents 	= eblog.editor.getContent();

		// Try to break the parts with the read more.
		var val	= editorContents.split( '<hr id="system-readmore" />' );

		if( val.length > 1 )
		{
			// It has a read more tag
			var intro		= $.sanitizeHTML( val[ 0 ] );
			var fulltext	= $.sanitizeHTML( val[ 1 ] );
			var content 	= intro + '<hr id="system-readmore" />' + fulltext;
		}
		else
		{
			// Since there is no read more tag here, the first index is always the full content.
			var content 	= $.sanitizeHTML( editorContents );
		}

		if ($.browser.msie && (parseInt($.browser.version) < 9)) {

			function ieInnerHTML(obj, convertToLowerCase) {
			    var zz = obj.innerHTML ? String(obj.innerHTML) : obj
			       ,z  = zz.match(/(<.+[^>])/g);

			    if (z) {
			     for ( var i=0;i<z.length;(i=i+1) ){
			      var y
			         ,zSaved = z[i]
			         ,attrRE = /\=[a-zA-Z\.\:\[\]_\(\)\&\$\%#\@\!0-9\/]+[?\s+|?>]/g
			      ;

			      z[i] = z[i]
			              .replace(/([<|<\/].+?\w+).+[^>]/,
			                 function(a){return a;
			               });
			      y = z[i].match(attrRE);

			      if (y){
			        var j   = 0
			           ,len = y.length
			        while(j<len){
			          var replaceRE =
			               /(\=)([a-zA-Z\.\:\[\]_\(\)\&\$\%#\@\!0-9\/]+)?([\s+|?>])/g
			             ,replacer  = function(){
			                  var args = Array.prototype.slice.call(arguments);
			                  return '="'+(convertToLowerCase
			                          ? args[2].toLowerCase()
			                          : args[2])+'"'+args[3];
			                };
			          z[i] = z[i].replace(y[j],y[j].replace(replaceRE,replacer));
			          j+=1;
			        }
			       }
			       zz = zz.replace(zSaved,z[i]);
			     }
			   }
			  return zz;
			}

			content = ieInnerHTML(content);
		}

		$( '#write_content_hidden' ).val( content );
	}


});



EasyBlog.require()
	.script(
		"dashboard/editor",
		"dashboard/medialink"
	)
	.done(function($){
		$("#wysiwyg").implement(EasyBlog.Controller.Dashboard.Editor, {});
		$(".ui-medialink").implement(EasyBlog.Controller.Dashboard.MediaLink);
	});

</script>

<div id="eblog-wrapper">
<form name="adminForm" id="blogForm" method="post" action="index.php">
	<table class="noshow" cellpadding="5" cellspacing="5">
		<tr>
			<td valign="top" width="60%" style="padding-right:8px">
				<ul class="list-form reset-ul" style="margin-left:10px">
					<li>
		    			<div class="clearfix"><label for="title" class="fsl ffa fwb"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_TITLE'); ?></label></div>
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_TITLE_DESC' );?></div>
							<input type="text" name="title" id="title" value="<?php echo $blog->title; ?>" class="inputbox write-title width-full ffa fsl" />
						</div>
					</li>
					<li>
		    			<div class="clearfix"><label for="slug" class="fsl ffa fwb"><?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_PERMALINK'); ?></label></div>
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_BLOGS_BLOG_PERMALINK_DESC' );?></div>
							<input type="text" name="permalink" id="permalink" value="<?php echo $blog->permalink;?>" class="inputbox write-slug width-full" />
						</div>
				  	</li>
					<li style="margin-top:15px;padding-top:15px;border-top:1px dotted #ddd">
						<div id="editor-write_body" class="clearfix">

							<div id="editor-content" class="clearfix mbs">

								<div class="ui-medialink">

									<div class="ui-togmenugroup clearfix pas">

										<a href="javascript:void(0);" class="ico-dglobe float-l prel mrs ui-togmenu olderPosts" togbox="olderPosts">
											<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_EDITOR_INSERT_LINK_ADD_TO_CONTENT'); ?></b>
											<span class="ui-toolnote">
												<i></i>
												<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_EDITOR_INSERT_LINK_ADD_TO_CONTENT'); ?></b>
												<span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_EDITOR_INSERT_LINK_ADD_TO_CONTENT_TIPS'); ?></span>
											</span>
										</a>

										<i></i>
								        <?php
								            $this->editorName = 'write_content';
											echo $this->loadTemplate( 'images' );
										?>

										<i></i>
										<a class="float-l ico-dvideo prel" href="javascript: void(0);" onclick="eblog.dashboard.videos.showForm('write_content');" title="<?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO' );?>">
											<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO' );?></b>
											<span class="ui-toolnote">
												<i></i>
												<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO' );?></b>
												<span><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO_TIPS' ); ?></span>
											</span>
										</a>
									</div>

									<div class="ui-togbox olderPosts">
										<div class="pas search-field" style="background:#f5f5f5;">
								            <div class="pas mrl">
												<input type="text" id="search-content" class="input width-half" onblur="if (this.value == '') {this.value = '<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_PREVIOUS_POST'); ?>';}" onfocus="if (this.value == '<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_PREVIOUS_POST'); ?>') {this.value = '';}" value="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_PREVIOUS_POST'); ?>" />
												<input type="button" onclick="eblog.editor.search.load('write_content');return false;" value="<?php echo JText::_('COM_EASYBLOG_SEARCH'); ?>" class="buttons" style="height:26px!important" />
											</div>
										</div>
										<div class="search-results-content"></div>
									</div>

									<div class="ui-togbox miniManager"></div>

								</div>

							</div>

							<div id="wysiwyg" class="clearfix">
			    				<?php echo $editor->display( 'write_content', EasyBlogHelper::getHelper('String')->escape( $this->content ), '100%', '550', '10', '10' , array('pagebreak','ninjazemanta','image') ); ?>
			    				<input id="write_content_hidden" value="" type="hidden" name="write_content_hidden"/>
			    			</div>

						</div>
					</li>
				</ul>
			</td>
			<td valign="top" width="38%">
			<?php
				$pane	= JPane::getInstance('sliders', array('allowAllClose' => true));

				echo $pane->startPane("content-pane");
				echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_PUBLISHING_OPTIONS' ) , "detail-page" );
				echo $this->loadTemplate( 'publishing' );
				echo $pane->endPanel();
				echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_FORMAT' ), "blog-format" );
				echo $this->loadTemplate( 'blog_format' );
				echo $pane->endPanel();
				echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_IMAGE' ), "blog-image" );
				echo $this->loadTemplate( 'blog_image' );
				echo $pane->endPanel();
				// @rule: Only show autoposting panel when necessary
				if(
				$this->acl->rules->update_facebook && $this->config->get( 'integrations_facebook' )  ||
				$this->acl->rules->update_twitter && $this->config->get( 'integrations_twitter' )  ||
				$this->acl->rules->update_linkedin && $this->config->get( 'integrations_linkedin' ) )
				{
					echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOG_AUTOPOSTING' ) , "autoposting-page" );
					echo $this->loadTemplate( 'autoposting' );
					echo $pane->endPanel();

				}
				echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_TAGS' ), "metadata-page" );
				echo $this->loadTemplate( 'tagging' );
				echo $pane->endPanel();
				echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOG_LOCATION' ) , "location-page" );
				echo $this->loadTemplate( 'location' );
				echo $pane->endPanel();
				echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_METADATA' ), "params-page" );
				echo $this->loadTemplate( 'metadata' );
				echo $pane->endPanel();
				echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_TRACKBACKS' ), "metadata-page" );
				echo $this->loadTemplate( 'trackbacks' );
				echo $pane->endPanel();

				if( $this->config->get( 'layout_dashboard_zemanta' ) && $this->config->get( 'layout_dashboard_zemanta_api') != '' )
				{
					echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_ZEMANTA' ), "zemanta-page" );
					echo $this->loadTemplate( 'zemanta' );
					echo $pane->endPanel();
				}

				echo $pane->endPane();
			?>
			</td>
		</tr>
	</table>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="savenew" value="0" id="savenew" />
<input type="hidden" name="ispending" value="<?php echo $acl->rules->publish_entry ? '0' : '1'; ?>" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="c" value="blogs" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="metaid" value="<?php echo $this->meta->id; ?>" />
<input type="hidden" name="blogid" value="<?php echo $blog->id;?>" />
<input type="hidden" name="draft_id" id="draft_id" value="<?php echo $draft->id;?>" />
<input type="hidden" name="under_approval" value="<?php echo $this->pending_approval; ?>" />
</form>
</div>
