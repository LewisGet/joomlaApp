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
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class EasyBlogAdminView extends JView
{
	function getModel( $name = null )
	{
		static $model = array();

		if( !isset( $model[ $name ] ) )
		{
			$path	= EBLOG_ADMIN_ROOT . DS . 'models' . DS . JString::strtolower( $name ) . '.php';

			jimport('joomla.filesystem.path');
			if ( !JFile::exists( $path ))
			{
				JError::raiseWarning( 0, 'Model file not found.' );
			}

			$modelClass		= 'EasyBlogModel' . ucfirst( $name );

			if( !class_exists( $modelClass ) )
				require_once( $path );


			$model[ $name ] = new $modelClass();
		}

		return $model[ $name ];
	}

	function renderCheckbox( $configName , $state )
	{
		ob_start();
	?>
		<label class="option-enable<?php echo $state == 1 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_EASYBLOG_YES_OPTION' );?></span></label>
		<label class="option-disable<?php echo $state == 0 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_EASYBLOG_NO_OPTION' ); ?></span></label>
		<input name="<?php echo $configName; ?>" value="<?php echo $state;?>" type="radio" id="<?php echo $configName; ?>" class="radiobox" checked="checked" />
		<div style="clear:both;"></div>
	<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function renderFilters( $options = array() , $value , $element )
	{
		ob_start();

		?>

		<script type="text/javascript">
		EasyBlog.ready(function($){
			$(".eblog-filter").click(function(){

				$('#' + $(this).data('element'))
					.val($(this).data('key'));
				submitform();
			});
		});
		</script>

		<?php

		foreach( $options as $key => $val )
		{
		?>
		<a class="eblog-filter<?php echo $value == $key ? ' eblog-filter-active' : '';?>" href="javascript:void(0);" data-element="<?php echo $element;?>" data-key="<?php echo $key;?>"><?php echo JText::_( $val ); ?></a>
		<?php
		}
		?>
		<input type="hidden" name="filter_type" id="filter_type" value="<?php echo $value;?>" />
		<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
