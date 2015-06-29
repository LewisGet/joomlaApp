<?php
/*------------------------------------------------------------------------
 # com_j2store - J2 Store v 1.0
# ------------------------------------------------------------------------
# author    Sasi varna kumar - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2012 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://j2store.org
# Technical Support:  Forum - http://j2store.org/forum/index.html
-------------------------------------------------------------------------*/



// no direct access
defined('_JEXEC') or die('Restricted access');

class J2StoreOrdersHelper {


	function sendUserEmail($user_id, $order_id, $payment_status, $order_status, $order_state_id)
	{
		$mainframe =& JFactory::getApplication();

		// grab config settings for sender name and email
		$config     = &JComponentHelper::getParams('com_j2store');
		$j2params = &JComponentHelper::getParams('com_content');
		$mailfrom   = $config->get( 'emails_defaultemail', $mainframe->getCfg('mailfrom') );
		$fromname   = $config->get( 'emails_defaultname', $mainframe->getCfg('fromname') );


		$sitename   = $config->get( 'sitename', $mainframe->getCfg('sitename') );
		$siteurl    = $config->get( 'siteurl', JURI::root() );

		//now get the order table's id based on order id
		$id = J2StoreOrdersHelper::_getOrderKey($order_id);

		//now get the receipient
		$recipient = J2StoreOrdersHelper::_getRecipient($id);

		if($user_id && empty($recipient->billing_first_name)) {
			$recipient->name = JFactory::getUser()->name;
		} else {
			$recipient->name = $recipient->billing_first_name.' '.$recipient->billing_last_name;
		}

		$html = J2StoreOrdersHelper::_getHtmlFormatedOrder($id, $user_id);

		$mailer =& JFactory::getMailer();
		$mode = 1;

		$subject = JText::sprintf('J2STORE_ORDER_USER_EMAIL_SUB', $recipient->name, $sitename);

		$msg = '';
		$msg .= $html;

		$admin_emails = $config->get('admin_email') ;
		$admin_emails = explode(',',$admin_emails ) ;

		//send email
		if ($recipient)
		{
			$mailer->addRecipient($recipient->user_email);
			//   $mailer->addCC( $config->get('admin_email'));
			$mailer->addCC( $admin_emails );
			$mailer->setSubject( $subject );
			$mailer->setBody($msg);
			$mailer->IsHTML($mode);
			$mailer->setSender(array( $mailfrom, $fromname ));
			$mailer->send();
		}

		return true;
	}



	function _getUser($uid)
	{

		$db =& JFactory::getDBO();
		$q = "SELECT name, email FROM #__users "
		. "WHERE id = {$uid}"
		;

		$db->setQuery($q);
		$user_email = $db->loadObject();

		if ($error = $db->getErrorMsg()) {
			JError::raiseError(500, $error);
			return false;
		}

		return $user_email;
	}


	function _getRecipient($orderpayment_id) {


		$db =& JFactory::getDBO();
		$q = "SELECT user_email,user_id,billing_first_name,billing_last_name FROM #__j2store_orderinfo"
		. " WHERE orderpayment_id = {$orderpayment_id}"
		;
		$db->setQuery($q);
		$user_email = $db->loadObject();

		if ($error = $db->getErrorMsg()) {
			JError::raiseError(500, $error);
			return false;
		}

		return $user_email;
	}


	function _getOrderKey($order_id) {

		$db = &JFactory::getDBO();
		$query = 'SELECT id FROM #__j2store_orders WHERE order_id='.$db->Quote($order_id);
		$db->setQuery($query);
		return $db->loadResult();
	}


	function _getHtmlFormatedOrder($id, $user_id) {

		$app = &JFactory::getApplication();
		$j2storeparams   = &JComponentHelper::getParams('com_j2store');


		$sitename   = $j2storeparams->get( 'sitename', $app->getCfg('sitename') );
		$siteurl    = $j2storeparams->get( 'siteurl', JURI::root() );

		$html = ' ';

		JLoader::register( "J2StoreViewOrders", JPATH_SITE."/components/com_j2store/views/orders/view.html.php" );

		$config = array();
		$config['base_path'] = JPATH_SITE."/components/com_j2store";
		if ($app->isAdmin())
		{
			// finds the default Site template
			$db = JFactory::getDBO();
			//depricated table name changed to template_styles
			//$query = "SELECT template FROM #__templates_menu WHERE `client_id` = '0' AND `menuid` = '0';";

			//$query = "SELECT template FROM #__template_styles WHERE `client_id` = '0' AND `menuid` = '0';";
			$query = "SELECT template FROM #__template_styles WHERE `client_id` = '0'";
			$db->setQuery( $query );
			$template = $db->loadResult();

			jimport('joomla.filesystem.file');
			if (JFile::exists(JPATH_SITE.'/templates/'.$template.'/html/com_j2store/orders/orderemail.php'))
			{
				// (have to do this because we load the same view from the admin-side Orders view, and conflicts arise)
				$config['template_path'] = JPATH_SITE.'/templates/'.$template.'/html/com_j2store/orders';
			}
		}

		$view = new J2StoreViewOrders( $config );

		require_once(JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'models'.DS.'orders.php');
		$model =  new J2StoreModelOrders();
		//lets set the id first
		$model->setId($id);

		$order = $model->getTable( 'orders' );
		$order->load( $model->getId() );
		$orderitems = &$order->getItems();
		$row = $model->getItem();

		if(!$user_id) {
			$isGuest = true;
		}else{
			$isGuest=false;
		}

		$view->set( '_controller', 'orders' );
		$view->set( '_view', 'orders' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', false);
		$view->setModel( $model, true );
		$view->assign( 'row', $row );
		$show_tax = $j2storeparams->get('show_tax_total');
		$view->assign( 'show_tax', $show_tax );
		foreach ($orderitems as &$item)
		{
			$item->orderitem_price = $item->orderitem_price + floatval( $item->orderitem_attributes_price );
			$taxtotal = 0;
			if($show_tax)
			{
				$taxtotal = ($item->orderitem_tax / $item->orderitem_quantity);
			}
			$item->orderitem_price = $item->orderitem_price + $taxtotal;
			$item->orderitem_final_price = $item->orderitem_price * $item->orderitem_quantity;
			$order->order_subtotal += ($taxtotal * $item->orderitem_quantity);
		}

		$view->assign( 'order', $order );
		$view->assign( 'isGuest', $isGuest);
		$view->assign( 'sitename', $sitename);
		$view->assign( 'siteurl', $siteurl);
		$view->assign( 'params', $j2storeparams);
		$view->setLayout( 'orderemail' );

		//$this->_setModelState();
		ob_start();
		$view->display();
		$html .= ob_get_contents();
		ob_end_clean();
		return $html;
	}


	function getAddress($user_id) {

		$db = &JFactory::getDBO();
		$query = 'SELECT tbl.*,c.country_name,z.zone_name'
		.' FROM #__j2store_address AS tbl'
		.' LEFT JOIN #__j2store_countries AS c ON tbl.country_id=c.country_id'
		.' LEFT JOIN #__j2store_zones AS z ON tbl.zone_id=z.zone_id'
		.' WHERE tbl.user_id='.(int) $user_id;
		$db->setQuery($query);
		return $db->loadObject();
	}

}
?>
