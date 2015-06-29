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

jimport('joomla.utilities.date');
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_easyblog' .  DS . 'helpers' . DS . 'helper.php' );

class EasyBlogDateHelper
{
	/*
	 * return the jdate with the correct specified timezone offset
	 * param : raw date string (date with no offset yet)
	 * return : JDate object
	 */
	public static function dateWithOffSet($str='')
	{
		$userTZ = EasyBlogDateHelper::getOffSet();
		$date	= new JDate($str);

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$user		= JFactory::getUser();
			$config     = EasyBlogHelper::getConfig();
			$jConfig    = JFactory::getConfig();

			// temporary ignore the dst in joomla 1.6

			if($user->id != 0)
			{
				$userTZ	= $user->getParam('timezone');
			}

			if(empty($userTZ))
			{
				$userTZ	= $jConfig->get('offset');
			}

			$tmp = new DateTimeZone( $userTZ );
			$date->setTimeZone( $tmp );
		}
		else
		{
			$date->setOffset( $userTZ );
		}

		return $date;
	}

	public static function getDate($str='')
	{
		return EasyBlogDateHelper::dateWithOffSet($str);
	}

	function geRawUnixTimeOld($str='')
	{
		$tzoffset 	= EasyBlogDateHelper::getOffSet();
		$date 		= JFactory::getDate( $str );

		$newdate = mktime( ($date->toFormat('%H')  - $tzoffset),
							$date->toFormat('%M'),
							$date->toFormat('%S'),
							$date->toFormat('%m'),
							$date->toFormat('%d'),
							$date->toFormat('%Y'));
		return $newdate;
	}

	public static function getOffSet16($numberOnly = false)
	{
		jimport('joomla.form.formfield');

		$user		= JFactory::getUser();
		$config     = EasyBlogHelper::getConfig();
		$jConfig    = JFactory::getConfig();

		// temporary ignore the dst in joomla 1.6

		if($user->id != 0)
		{
			$userTZ	= $user->getParam('timezone');
		}

		if(empty($userTZ))
		{
			$userTZ	= $jConfig->get('offset');
		}

		if( $numberOnly )
		{
			$newTZ  	= new DateTimeZone($userTZ);
			$dateTime   = new DateTime( "now" , $newTZ );

			$offset		= $newTZ->getOffset( $dateTime ) / 60 / 60;
			return $offset;
		}
		else
		{
			//timezone string
			return $userTZ;
		}
	}

	public static function getOffSet( $numberOnly	= false )
	{
		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			//return a timezone object
			return EasyBlogDateHelper::getOffSet16($numberOnly);
		}

		$mainframe	= JFactory::getApplication();
		$user		= JFactory::getUser();
		$config     = EasyBlogHelper::getConfig();

		$userTZ     = '';
		$dstOffset  = $config->get('main_dstoffset', 0);


		if($user->id != 0)
		{
			$userTZ	= $user->getParam('timezone') + $dstOffset;
		}

		//if user did not set timezone, we use joomla one.
		if(empty($userTZ))
		{
			$userTZ	= $mainframe->getCfg('offset') + $dstOffset;
		}

		return $userTZ;
	}

	public static function enableDateTimePicker()
	{
		$document	= JFactory::getDocument();

		// load language for datetime picker
		$html = '
		<script type="text/javascript">
		/* Date Time Picker */
		var sJan			= "'.JText::_('JAN').'";
		var sFeb			= "'.JText::_('FEB').'";
		var sMar			= "'.JText::_('MAR').'";
		var sApr			= "'.JText::_('APR').'";
		var sMay			= "'.JText::_('MAY').'";
		var sJun			= "'.JText::_('JUN').'";
		var sJul			= "'.JText::_('JUL').'";
		var sAug			= "'.JText::_('AUG').'";
		var sSep			= "'.JText::_('SEP').'";
		var sOct			= "'.JText::_('OCT').'";
		var sNov			= "'.JText::_('NOV').'";
		var sDec			= "'.JText::_('DEC').'";
		var sAm				= "'.JText::_('AM').'";
		var sPm				= "'.JText::_('PM').'";
		var btnOK			= "'.JText::_('COM_EASYBLOG_SAVE_BUTTON').'";
		var btnReset		= "'.JText::_('COM_EASYBLOG_RESET').'";
		var btnCancel		= "'.JText::_('COM_EASYBLOG_CANCEL').'";
		var sNever			= "'.JText::_('COM_EASYBLOG_NEVER').'";
		</script>';

		$document->addCustomTag( $html );
	}

	function getLapsedTime( $time )
	{
		$now	= JFactory::getDate();
		$end	= JFactory::getDate( $time );
		$time	= $now->toUnix() - $end->toUnix();

		$tokens = array (
							31536000 	=> 'COM_EASYBLOG_X_YEAR',
							2592000 	=> 'COM_EASYBLOG_X_MONTH',
							604800 		=> 'COM_EASYBLOG_X_WEEK',
							86400 		=> 'COM_EASYBLOG_X_DAY',
							3600 		=> 'COM_EASYBLOG_X_HOUR',
							60 			=> 'COM_EASYBLOG_X_MINUTE',
							1 			=> 'COM_EASYBLOG_X_SECOND'
						);

		foreach( $tokens as $unit => $key )
		{
			if ($time < $unit)
			{
				continue;
			}

			$units	= floor( $time / $unit );

			$string = $units > 1 ?  $key . 'S' : $key;
			$string = $string . '_AGO';

			$text   = JText::sprintf(strtoupper($string), $units);
			return $text;
		}

	}

	public static function toFormat($jdate, $format='%Y-%m-%d %H:%M:%S')
	{
		if(is_null($jdate))
		{
			$jdate  = new JDate();
		}

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			// There is no way to have cross version working, except for detecting % in the format
			if( JString::stristr( $format , '%') === false )
			{
				return $jdate->format( $format , true );
			}
			return $jdate->toFormat( $format, true );
		}
		return $jdate->toFormat( $format );
	}
}
