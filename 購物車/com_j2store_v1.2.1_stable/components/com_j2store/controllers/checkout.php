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

jimport('joomla.application.component.controller');
JLoader::register( 'J2StoreHelperCart', JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'helpers'.DS.'cart.php');

class J2StoreControllerCheckout extends JController
{

	var $_order        = null;
	var $defaultShippingMethod = null; // set in constructor
	var $initial_order_state   = 4;
	var $_cartitems = null;

	function __construct()
	{

		parent::__construct();
		$cart_helper = new J2StoreHelperCart();
		$items = $cart_helper->getProductsInfo();
		$this->_cartitems = $items;
		$params = &JComponentHelper::getParams('com_j2store');
		$this->defaultShippingMethod = $params->get('defaultShippingMethod', '1');
		// create the order object
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'tables' );
		$this->_order = JTable::getInstance('Orders', 'Table');
	}

	function display() {
		$app = &JFactory::getApplication();
		$user 		=	& JFactory::getUser();
		//	$guest =(int) JRequest::getVar( 'guest', '0' );
		$params = &JComponentHelper::getParams('com_j2store');
		$view = $this->getView( 'checkout', 'html' );
		$items = $this->_cartitems;
		$task = JRequest::getVar('task');

		if (empty($items) && $task != 'confirmPayment' )
		{
			$msg = JText::_('J2STORE_NO_ITEMS_IN_CART');
			$link = JRoute::_('index.php');
			$app->redirect($link, $msg);
		}

		if(!$user->id) {
			// Display a form for selecting either to register or to login
			//$view->setLayout('form');
			if(JVERSION==1.7) JRequest::setVar( 'layout', 'form');
			else $view->setLayout( 'form');
			parent::display();
		} else {

			//	$link = JRoute::_('index.php?option=com_j2store&view=checkout&task=begin');
			//	$app->redirect($link);
			$this->begin();
		}

	}

	function begin() {
		$app = &JFactory::getApplication();
		$user 		=	& JFactory::getUser();
		$params = &JComponentHelper::getParams('com_j2store');
		$values = JRequest::get('post');
		//if guest checkout is not allowed, then we cannot allow them from beyond this point.

		//prepare cart and the payment
		$cart_helper = new J2StoreHelperCart();
		$items = $cart_helper->getProductsInfo();
		//$items = $this->_cartitems;
		if(count($items) < 1) {
			$msg = JText::_('J2STORE_NO_ITEMS_IN_CART');
			$link = JRoute::_('index.php');
			$app->redirect($link, $msg);
		}

		//if guest check out is not allowed. Do not allow
		//restrict user access
		if(!$params->get('allow_guest_checkout') && empty($user->id)) {
			$msg = JText::_('J2STORE_ERROR_REGISTER_USERS_ONLY');
			$link = JRoute::_('index.php?option=com_j2store&view=mycart');
			$app->redirect($link, $msg);
		}

		//if guest checkout is allowed, then check for the presence of an email.
		if($params->get('allow_guest_checkout') && empty($user->id) && empty($values['guest_mail']) ) {
			$msg = JText::_('J2STORE_ERROR_GUEST_EMAIL_REQUIRED');
			$link = JRoute::_('index.php?option=com_j2store&view=checkout');
			$app->redirect($link, $msg);
		}


		/*echo $values['guest_mail'];
		echo ' ---';
		echo empty($values['guest_mail']);
		echo '---';
		echo !empty($values['guest_mail']);
		echo '---';
		exit;
		*/
		$guest = 0;
		//if guest email is present, set it to session.
		if($params->get('allow_guest_checkout') && !empty($values['guest_mail'])) {
			//validate the email
			if(filter_var($values['guest_mail'], FILTER_VALIDATE_EMAIL) !== false ) {
				$session = JFactory::getSession();
				$session->set('guest_mail', $values['guest_mail']);
				$guest = 1;
			}else {
				$msg = JText::_('J2STORE_ERROR_GUEST_EMAIL_WRONG');
				$link = JRoute::_('index.php?option=com_j2store&view=checkout');
				$app->redirect($link, $msg);
			}

		}

		$view = $this->getView( 'checkout', 'html' );

		$task = JRequest::getVar('task');

		$order =& $this->_order;
		$order = $this->populateOrder(false);

		//minimum order value check
		if(!$this->checkMinimumOrderValue($order)) {
			$msg = JText::_('J2STORE_ERROR_MINIMUM_ORDER_VALUE').J2StorePrices::number($params->get('global_minordervalue'));
			$link = JRoute::_('index.php?option=com_j2store&view=mycart');
			$app->redirect($link, $msg);
		}

		//shipping
		// Checking whether shipping is required
		$showShipping = false;

		$cartsModel = $this->getModel('mycart');
		if ($isShippingEnabled = $cartsModel->getShippingIsEnabled())
		{
			$showShipping = true;
			$this->setShippingMethod();
		}

		// get the order totals
		//$order->calculateTotals();

		// now that the order object is set, get the orderSummary html
		$html = $this->getOrderSummary();



		$model		= &$this->getModel('checkout');
		$bill_address = $model->checkBillingAddress();
		$ship_address = $model->checkShippingAddress();

		$bill_country = $model->getCountryList('billing[country_id]','billing:country',$bill_address->country_id);
		$view->assign('bill_country', $bill_country);
		$ship_country = $model->getCountryList('shipping[country_id]','shipping:country',$ship_address->country_id);
		$view->assign('ship_country', $ship_country);

		if($bill_address) $view->assign('bill_address', $bill_address);
		if($ship_address) $view->assign('ship_address', $ship_address);

		//Set display
		$view->set( '_doTask', true);
		//Get and Set Model
		$view->setModel( $model, true );

		//assign the terms and conditions link
		if( $params->get('termsid') ){
			$tos_link = JRoute::_('index.php?option=com_content&view=article&tmpl=component&id='.$params->get('termsid'));
		}else{
			$tos_link=null;
		}

		$view->assign( 'tos_link', $tos_link);
		$view->assign( 'showShipping', $showShipping );
		$view->assign('values', $values);
		$view->assign('guest', $guest);
		$view->set( 'hidemenu', false);
		$view->assign( 'order', $order );
		$view->assign('params', $params);
		$view->assign( 'orderSummary', $html );

		$showPayment = true;
		if ((float)$order->order_total == (float)'0.00')
		{
			$showPayment = false;
		}
		$view->assign( 'showPayment', $showPayment );

		require_once (JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'helpers'.DS.'plugin.php');
		$payment_plugins = J2StoreHelperPlugin::getPluginsWithEvent( 'onJ2StoreGetPaymentPlugins' );

		$dispatcher =& JDispatcher::getInstance();
		JPluginHelper::importPlugin ('j2store');

		$plugins = array();
		if ($payment_plugins)
		{
			foreach ($payment_plugins as $plugin)
			{
				$results = $dispatcher->trigger( "onJ2StoreGetPaymentOptions", array( $plugin->element, $order ) );
				if (in_array(true, $results, true))
				{
					$plugins[] = $plugin;
				}
			}
		}

		if (count($plugins) == 1)
		{
			$plugins[0]->checked = true;
			ob_start();
			$this->getPaymentForm( $plugins[0]->element );
			$html = json_decode( ob_get_contents() );
			ob_end_clean();
			$view->assign( 'payment_form_div', $html->msg );
		}

		$view->assign('plugins', $payment_plugins);

		if(JVERSION==1.7) JRequest::setVar( 'layout', 'default');
		else $view->setLayout( 'default');
		//$view->setLayout('default');

		$view->display();
	}

	public function ajaxGetZoneList() {

		$app = &JFactory::getApplication();
		$model = $this->getModel('checkout');
		$post = JRequest::get('post');
		$country_id = $post['country_id'];
		$zone_id = $post['zone_id'];
		$name=$post['field_name'];;
		$id=$post['field_id'];
		if($country_id) {
			$zones = $model->getZoneList($name,$id,$country_id,$zone_id);
			echo $zones;
		}
		$app->close();
	}


	function register(){

		$mainframe = &JFactory::getApplication();
		JRequest::checkToken() or jexit('Invalid Token');
		$register_data = JRequest::get('post');

		if(empty($register_data['email']) || empty($register_data['confirm_mail'])
				|| empty($register_data['password']) || empty($register_data['password2']) )  {
			$link = JRoute::_('index.php?option=com_j2store&view=checkout');
			$msg = JText::_('J2STORE_ALL_FIELDS_REQUIRED');
			$mainframe->redirect($link, $msg);
		}

		if($register_data['email'] != $register_data['confirm_mail']) {
			$link = JRoute::_('index.php?option=com_j2store&view=checkout');
			$msg = JText::_('J2STORE_EMAIL_DOES_NOT_MATCH');
			$mainframe->redirect($link, $msg);
		}

		if($register_data['password'] != $register_data['password2']) {
			$link = JRoute::_('index.php?option=com_j2store&view=checkout');
			$msg = JText::_('J2STORE_PASSWORD_DOES_NOT_MATCH');
			$mainframe->redirect($link, $msg);
		}

		$user_id = JFactory::getUser()->id;
		//user id must be empty as we are registering a new user

		if(empty($user_id)) {
			//  Register an User
			require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'library'.DS.'user.php');
			$userHelper = new J2StoreHelperUser;

			if ($userHelper->emailExists($register_data['email']))
			{
				// TODO user already exists
				$error= '1';
				$msg = JText::_('J2STORE_EMAIL_ALREADY_EXISTS');
			}
			else
			{
				$email = $register_data['email'];
				//for the name field strip the @ and get the name alone
				//$name = substr($email, strpos($email,"<")+1, strrpos($email, "@")-strpos($email,"<")-1);
				$s = explode("@",$email);
				$t = explode("<",$s[0]);
				$name = end($t);
				// create the details array with new user info
				$details = array(
						'email' =>  $register_data['email'],
						'name' => $name,
						'username' =>  $register_data['email'],
						'password' => $register_data['password'],
						'password2'=> $register_data['password2']
				);

				// create the new user
				$msg = $this->getError();
				$user = $userHelper->createNewUser($details, $msg);

				$userHelper->login(
						array('username' => $user->username, 'password' => $details['password'])
				);
			}

			if($error) {
				$msg = JText::_('J2STORE_ERROR_IN_SAVING_USER').'&nbsp;'.$msg;
				$link = JRoute::_('index.php?option=com_j2store&view=checkout');
				$mainframe->redirect($link, $msg);
			}
		}
		else {
			$link = JRoute::_('index.php?option=com_j2store&view=checkout&task=begin');
			$msg = JText::_('J2STORE_REGISTRATION_SUCCESSFULL');
		}
		$mainframe->redirect($link, $msg);
	}

	function getOrderSummary()
	{
		// get the order object
		$order =& $this->_order;
		$model = $this->getModel('mycart');
		$view = $this->getView( 'checkout', 'html' );
		$view->set( '_controller', 'checkout' );
		$view->set( '_view', 'checkout' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', true);
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$params = &JComponentHelper::getParams('com_j2store');
		$show_tax = $params->get('show_tax_total');
		$view->assign( 'show_tax', $params->get('show_tax_total'));
		$view->assign( 'params', $params);
		$view->assign( 'order', $order );

		$orderitems = $order->getItems();
		//	print_r($orderitems);
		require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'prices.php');

		$tax_sum = 0;
		foreach ($orderitems as &$item)
		{
			$item->price = $item->orderitem_price + floatval( $item->orderitem_attributes_price );
			$tax = 0;
			if ($show_tax)
			{

				$product_tax_rate = J2StorePrices::getItemTax($item->product_id);
				$tax = $product_tax_rate * ($item->orderitem_price + floatval( $item->orderitem_attributes_price ));

				$item->price = $item->orderitem_price + floatval( $item->orderitem_attributes_price ) + $tax;
				$item->orderitem_final_price = $item->price * $item->orderitem_quantity;

				$order->order_subtotal += ($tax * $item->orderitem_quantity);
			}
			$tax_sum += ($tax * $item->orderitem_quantity);
		}

		// Checking whether shipping is required
		$showShipping = false;

		if ($isShippingEnabled = $model->getShippingIsEnabled())
		{
			$showShipping = true;
			$view->assign( 'shipping_total', $order->getShippingTotal() );
		}
		$view->assign( 'showShipping', $showShipping );

		$view->assign( 'orderitems', $orderitems );
		$view->setLayout( 'cartsummary' );

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	function populateOrder($guest = false)
	{
		$order =& $this->_order;
		$order->shipping_method_id = $this->defaultShippingMethod;

		//$items = $cart_helper->getProductsInfo();
		$items = $this->_cartitems;
		foreach ($items as $item)
		{
			$order->addItem( $item );
		}
		// get the order totals
		$order->calculateTotals();

		return $order;
	}


	function checkMinimumOrderValue($order) {

		$params = &JComponentHelper::getParams('com_j2store');

		$min_value = $params->get('global_minordervalue');
		if(!empty($min_value)) {
			if($order->order_subtotal >= $min_value) {
			 return true;
			} else {
			 return false;
			}
		} else {
			return true;
		}
	}


	//hipping method set

	function setShippingMethod()
	{

		// get the order object so we can populate it
		$order =& $this->_order; // a TableOrders object (see constructor)
		//get rates
		$rate = & $this->getShippingRates();

		// set the shipping method
		$order->shipping = new JObject();
		$order->shipping->shipping_price      = $rate[0]->shipping_method_price;
		$order->shipping->shipping_extra      = $rate[0]->shipping_method_handling;
		$order->shipping->shipping_name       = $rate[0]->shipping_method_name;
		$order->shipping->shipping_method_id  = $rate[0]->shipping_method_id;

		// get the order totals
		$order->calculateTotals();
		return;
	}


	function getShippingHtml( $layout='shipping_yes' )
	{
		$order =& $this->_order;
		$params = &JComponentHelper::getParams('com_j2store');
		$html = '';
		$model = $this->getModel( 'Checkout', 'J2StoreModel' );
		$view   = $this->getView( 'checkout', 'html' );
		$view->set( '_controller', 'checkout' );
		$view->set( '_view', 'checkout' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', true);
		$view->setModel( $model, true );
		$view->setLayout( $layout );
		$rates = array();

		switch (strtolower($layout))
		{
			case "shipping_no":
				break;
			case "shipping_yes":
			default:
				$view->assign( 'params', $params );
				$view->assign( 'shipping_name', $order->shipping->shipping_name  );
				break;
		}

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function getShippingRates()
	{

		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'shipping.php');
		$shipping_helper = new J2StoreShipping;

		$order =& $this->_order;

		$rates = array();
		JModel::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'models');
		$model = JModel::getInstance('ShippingMethods', 'J2StoreModel');
		$model->setState('filter_published', 1);

		if ($methods = $model->getItemFront())
		{
			foreach( $methods as $method )
			{
				// filter the list of methods according to geozone
				$ratemodel = JModel::getInstance('ShippingRates', 'J2StoreModel');
				$ratemodel->setState('filter_shippingmethod', $method->id);
				if ($ratesexist = $ratemodel->getList())
				{
					$total = $shipping_helper->getTotal($method->id, $order->getItems());

					if ($total)
					{
						$total->shipping_method_type = $method->shipping_method_type;
						$rates[] = $total;
					}
				}
			}
		}
		return $rates;
	}

	function getPaymentForm($element='')
	{

		$values = JRequest::get('post');
		$html = '';
		$text = "";
		$user = JFactory::getUser();
		if (empty($element)) {
			$element = JRequest::getVar( 'payment_element' );
		}
		$results = array();
		$dispatcher    =& JDispatcher::getInstance();
		JPluginHelper::importPlugin ('j2store');

		$results = $dispatcher->trigger( "onJ2StoreGetPaymentForm", array( $element, $values ) );
		for ($i=0; $i<count($results); $i++)
		{
			$result = $results[$i];
			$text .= $result;
		}

		$html = $text;

		// set response array
		$response = array();
		$response['msg'] = $html;

		// encode and echo (need to echo to send back to browser)
		echo json_encode($response);

		return;
	}


	function registerNewUser ($values){

		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'tables' );

		//  Register an User
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'library'.DS.'user.php');
		$userHelper = new J2StoreHelperUser;

		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		if ($userHelper->emailExists($values['email']))
		{
			// TODO user already exists
			$response['error'] = '1';
			$response['msg'] = JText::_('J2STORE_EMAIL_ALREADY_EXISTS');
			$response['key'] = 'email';
			return $response;
		}
		else
		{
			// create the details array with new user info
			$details = array(
					'email' => $values['email'],
					'name' => $values['email'],
					'username' => $values['email']
			);

			// use a random password, and send password2 for the email
			jimport('joomla.user.helper');
			$details['password']    = $values['password'];
			$details['password2']   = $details['password'];

			// create the new user
			$msg = $this->getError();
			$user = $userHelper->createNewUser($details, $msg);

			$userHelper->login(
					array('username' => $user->username, 'password' => $details['password'])
			);

			if($user->id) {
				$model1 = $this->getModel('address');
				$success = $model1->save($values);

				if(!$success) {
					return false;
				}
			}
			if (empty($user->id))
			{
				return false;	// TODO what to do if creating new user failed?
			}

			return true;
		}
	}



	/**
	 * This method occurs before payment is attempted
	 * and fires the onPrePayment plugin event
	 *
	 * @return unknown_type
	 */
	function preparePayment()
	{
		// verify that form was submitted by checking token
		JRequest::checkToken() or jexit( 'J2StoreControllerCheckout::preparePayment - Invalid Token' );

		$app = &JFactory::getApplication();
		$user 		=	& JFactory::getUser();
		$params = &JComponentHelper::getParams('com_j2store');
		$session = JFactory::getSession();
		$begin_link = JRoute::_('index.php?option=com_j2store&view=checkout&task=begin');
		// 1. save the order to the table with a 'pre-payment' status

		// Get post values
		$values = JRequest::get('post');

		//get address model
		$address_model = $this->getModel('address');

		//lets first check and save the billing and shipping addresses

		if(count($values['billing']) > 1) {

			if(!$billing_address_id = $address_model->saveBillingAddress($values['billing'], $values['bill_id'])) {
				JError::raiseNotice( JText::_('J2STORE_ERROR_SAVING_BILLING_ADDRESS'), $address_model->getError() );
				return false;
			}
		}

		//save shipping address only when the shipping_make_same value is null
		if(empty($values['shipping_make_same']) || $values['shipping_make_same'] == 2) {
			if(count($values['shipping']) > 1) {
				if(!$shipping_address_id = $address_model->saveShippingAddress($values['shipping'], $values['ship_id'])) {
					JError::raiseNotice( JText::_('J2STORE_ERROR_SAVING_SHIPPING_ADDRESS'), $address_model->getError() );
					return false;
				}
			}
		} else {
			//save the billing address as the shipping address
			if(!$shipping_address_id = $address_model->saveShippingAddress($values['billing'], $values['ship_id'])) {
				JError::raiseNotice( JText::_('J2STORE_ERROR_SAVING_SHIPPING_ADDRESS'), $address_model->getError() );
				return false;
			}

		}

		//if billing address enabled or guest checkout enabled, then we need a billing address
		if(($params->get('show_billing_address') || $params->get('allow_guest_checkout')) && !$billing_address_id) {
			$msg = JText::_('J2STORE_ERROR_BILLING_ADDRESS_NEEDED');
			$app->redirect($begin_link, $msg);
			return false;
		}

		//lets check once more with the params selected in the backend.
		if($params->get('show_shipping_address') && !$shipping_address_id) {
			$msg = JText::_('J2STORE_ERROR_SHIPPING_ADDRESS_NEEDED');
			$app->redirect($begin_link, $msg);
			return false;
		}

		$order_id = time();
		$values['order_id'] = $order_id;
		// Get Order Object
		$order =& $this->_order;

		//now validate the payment and ToS

		If(!$this->validateSelectPayment($values, $order)) {
			$app->redirect($begin_link, $this->getError());
			return false;
		}


		// Save the orderitems with  status
		if (!$this->saveOrderItems($values))
		{	// Output error message and halt
		$app->redirect($begin_link, $this->getError());
		return false;
		}

		// Save the orderfiles
		if (!$this->saveOrderFiles($values))
		{
			$app->redirect($begin_link, $this->getError());
			return false;
		}



		//shipping
		// Checking whether shipping is required
		$showShipping = false;

		$cartsModel = $this->getModel('mycart');
		//TODO why dont we check the shipping address here
		if ($isShippingEnabled = $cartsModel->getShippingIsEnabled())
		{
			$showShipping = true;
			$this->setShippingMethod();
		}


		$orderpayment_type = $values['payment_plugin'];
		$transaction_status = JText::_( "J2STORE_TRANSACTION_INCOMPLETE" );
		// in the case of orders with a value of 0.00, use custom values
		if ( (float) $order->order_total == (float)'0.00' )
		{
			$orderpayment_type = 'free';
			$transaction_status = JText::_( "J2STORE_TRANSACTION_COMPLETE" );
		}

		//set order values
		$order->user_id = $user->id;
		$order->ip_address = $_SERVER['REMOTE_ADDR'];
		//get the customer note
		$customer_note = JRequest::getVar('customer_note', '', 'post', 'string');
		$order->customer_note = $customer_note;

		// Save an order with an Incomplete status
		$order->order_id = $order_id;
		$order->orderpayment_type = $orderpayment_type; // this is the payment plugin selected
		$order->transaction_status = $transaction_status; // payment plugin updates this field onPostPayment
		$order->order_state_id = 5; // default incomplete order state
		$order->orderpayment_amount = $order->order_total; // this is the expected payment amount.  payment plugin should verify actual payment amount against expected payment amount
		if ($order->save())
		{



			//set values for orderinfo table

			// send the order_id and orderpayment_id to the payment plugin so it knows which DB record to update upon successful payment
			$values["order_id"]             = $order->order_id;
			//$values["orderinfo"]            = $order->orderinfo;
			$values["orderpayment_id"]      = $order->id;
			$values["orderpayment_amount"]  = $order->orderpayment_amount;



			if($billing_address_id) {
				$bill_address = $address_model->getAddress($billing_address_id);
				//$order->billing_addr_id= $billing_address_id;
			}

			if($shipping_address_id) {
				$ship_address = $address_model->getAddress($shipping_address_id);
				//$order->shipping_addr_id=$shipping_address_id;
			}

			if(($params->get('show_billing_address') || $params->get('allow_guest_checkout')) && $bill_address) {

				foreach ($bill_address as $key=>$value) {
					$values['orderinfo']['billing_'.$key] = $value;
				}

				//compatability for payment plugins
				foreach ($bill_address as $key=>$value) {
					$values['orderinfo'][$key] = $value;
				}
				$values['orderinfo']['country'] = $bill_address['country_name'];
				$values['orderinfo']['state'] = $bill_address['zone_name'];
			}

			if($params->get('show_shipping_address') && $ship_address) {

				foreach ($ship_address as $key=>$value) {
					$values['orderinfo']['shipping_'.$key] = $value;
				}
			}

			//user email
			$user_email = ($user->id)?$user->email:$session->get('guest_mail');


			$values['orderinfo']['user_email'] = $user_email;
			$values['orderinfo']['user_id'] = $user->id;
			$values['orderinfo']['order_id'] = $order->order_id;
			$values['orderinfo']['orderpayment_id'] = $order->id;

			//now save the order info
			if (!$this->saveOrderInfo($values['orderinfo']))
			{
				// Output error message and halt
				$error = $this->getError();

			}

			if(isset($error)) {
				//JError::raiseNotice( 'Error Saving Order files', $this->getError() );
				$app->redirect($begin_link, $error);
				return false;
			}

		} else {
			// Output error message and halt
			JError::raiseNotice( 'J2STORE_ERROR_SAVING_ORDER', $order->getError() );
			return false;
		}

		// IMPORTANT: Store the order_id in the user's session for the postPayment "View Invoice" link

		$app->setUserState( 'j2store.order_id', $order->order_id );
		$app->setUserState( 'j2store.orderpayment_id', $order->id );


		// in the case of orders with a value of 0.00, we redirect to the confirmPayment page
		if ( (float) $order->order_total == (float)'0.00' )
		{
			$app->redirect( 'index.php?option=com_j2store&view=checkout&task=confirmPayment' );
			return;
		}

		$dispatcher    =& JDispatcher::getInstance();
		JPluginHelper::importPlugin ('j2store');

		$results = $dispatcher->trigger( "onJ2StorePrePayment", array( $values['payment_plugin'], $values ) );

		// Display whatever comes back from Payment Plugin for the onPrePayment
		$html = "";
		for ($i=0; $i<count($results); $i++)
		{
			$html .= $results[$i];
		}

		$model	= &$this->getModel('checkout');

		// get the order summary
		$summary = $this->getOrderSummary();

		// Set display
		$view = $this->getView( 'checkout', 'html' );
		$view->setLayout('prepayment');
		$view->set( '_doTask', true);
		$view->assign('order', $order);
		$view->assign('plugin_html', $html);
		$view->assign('orderSummary', $summary);
		$view->assign('bill_address', $bill_address);
		$view->assign('ship_address', $ship_address);
		$view->setModel( $model, true );

		$view->display();

		return;
	}


	/**
	 * Saves the order to the database
	 *
	 * @param $values
	 * @return unknown_type
	 */
	function saveOrder($values)
	{
		$error = false;
		$order =& $this->_order; // a TableOrders object (see constructor)
		//$order->bind( $values );
		$order->user_id = JFactory::getUser()->id;
		$order->ip_address = $_SERVER['REMOTE_ADDR'];
		//$this->setAddresses( $values );


		//get the items and add them to the order


		$cart_helper = new J2StoreHelperCart();
		$reviewitems = $cart_helper->getProductsInfo();

		foreach ($reviewitems as $reviewitem)
		{
			$order->addItem( $reviewitem );
		}

		$order->order_state_id = $this->initial_order_state;
		$order->calculateTotals();

		//$order->getInvoiceNumber();

		$model  = JModel::getInstance('Orders', 'J2StoreModel');
		//TODO: Do Something with Payment Infomation
		if ( $order->save() )
		{
			$model->setId( $order->id );

			// save the order items
			if (!$this->saveOrderItems())
			{
				// TODO What to do if saving order items fails?
				$error = true;
			}

			// save the order vendors
			if (!$this->saveOrderVendors())
			{
				// TODO What to do if saving order vendors fails?
				$error = true;
			}

			// save the order info
			if (!$this->saveOrderInfo())
			{
				// TODO What to do if saving order info fails?
				$error = true;
			}

			// save the order history
			if (!$this->saveOrderHistory())
			{
				// TODO What to do if saving order history fails?
				$error = true;
			}

			// save the order taxes
			if (!$this->saveOrderTaxes())
			{
				// TODO What to do if saving order taxes fails?
				$error = true;
			}

			// save the order shipping info
			if (!$this->saveOrderShippings())
			{
				// TODO What to do if saving order shippings fails?
				$error = true;
			}

			// save the order coupons
			if (!$this->saveOrderCoupons())
			{
				// TODO What to do if saving order coupons fails?
				$error = true;
			}
		}

		if ($error)
		{
			return false;
		}



		return true;
	}

	function saveOrderFiles($values){

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$user = &JFactory::getUser();
		$session = JFactory::getSession();

		$query->select('pf.productfile_id, oi.orderitem_id');
		$query->from('#__j2store_orderitems AS oi');
		$query->where('oi.order_id = '.$values['order_id']);
		$query->join('LEFT', '`#__j2store_productfiles` AS pf ON pf.product_id = oi.product_id');
		$db->setQuery($query);
		$file_items = $db->loadObjectList();

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'tables');
		//$row = JTable::getInstance('orderfiles','Table');

		foreach ($file_items as $file) {

			unset($row);
			$row = JTable::getInstance('orderfiles','Table');
			$row->orderitem_id=$file->orderitem_id;
			$row->productfile_id=$file->productfile_id;
			$row->limit_count=0;
			$row->user_id=$user->id ;
			if($session->get('guest_mail'))
				$row->user_email=$session->get('guest_mail');

			if ( !$row->save() )
			{
				$messagetype = 'notice';
				$message = JText::_( 'J2STORE_ERROR_SAVING_FILES_FAILED' )." - ".$row->getError();
				$this->setError( $message );
				return false;
			}
		}
		return true;
	}

	/**
	 * Saves each individual item in the order to the DB
	 *
	 * @return unknown_type
	 */
	function saveOrderItems($values)
	{
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'tables' );
		$order =& $this->_order;
		$order_id = $values['order_id'];
		//review things once again
		$cart_helper = new J2StoreHelperCart();
		$reviewitems = $cart_helper->getProductsInfo();

		foreach ($reviewitems as $reviewitem)
		{
			$order->addItem( $reviewitem );
		}

		$order->order_state_id = $this->initial_order_state;
		$order->calculateTotals();


		$items = $order->getItems();

		if (empty($items) || !is_array($items))
		{
			$this->setError( "saveOrderItems:: ".JText::_( "J2STORE_ORDER_SAVE_INVALID_ITEMS" ) );
			return false;
		}

		$error = false;
		$errorMsg = "";
		foreach ($items as $item)
		{
			$item->order_id = $order_id;

			if (!$item->save())
			{
				// track error
				$error = true;
				$errorMsg .= $item->getError();
			}
			else
			{



				// Save the attributes also
				if (!empty($item->orderitem_attributes))
				{
					$attributes = explode(',', $item->orderitem_attributes);
					foreach (@$attributes as $attribute)
					{
						unset($productattribute);
						unset($orderitemattribute);
						$productattribute = JTable::getInstance('ProductAttributeOptions', 'Table');
						$productattribute->load( $attribute );
						$orderitemattribute = JTable::getInstance('OrderItemAttributes', 'Table');
						$orderitemattribute->orderitem_id = $item->orderitem_id;
						$orderitemattribute->productattributeoption_id = $productattribute->productattributeoption_id;
						$orderitemattribute->orderitemattribute_name = $productattribute->productattributeoption_name;
						$orderitemattribute->orderitemattribute_price = $productattribute->productattributeoption_price;
						$orderitemattribute->orderitemattribute_code = $productattribute->productattributeoption_code;
						$orderitemattribute->orderitemattribute_prefix = $productattribute->productattributeoption_prefix;
						if (!$orderitemattribute->save())
						{
							// track error
							$error = true;
							$errorMsg .= $orderitemattribute->getError();
						}
					}
				}
			}
		}

		if ($error)
		{
			$this->setError( $errorMsg );
			return false;
		}
		return true;
	}


	function saveOrderInfo($values){

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'tables');
		$row = JTable::getInstance('orderinfo','Table');

		if (!$row->bind($values)) {
			$this->setError($row->getError());
			return false;
		}

		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}

		if (!$row->store()) {
			$this->setError($row->getError());
			return false;
		}

		return true;
	}

	function validateSelectPayment($values, $order) {

		$response = array();
		$response['msg'] = '';
		$response['error'] = '';

		if ($order->order_total > 0 )
		{
			if(empty($values['payment_plugin'])) {
				$response['msg'] =  JText::_('J2STORE_SELECT_A_PAYMENT_METHOD');
				$response['error'] = '1';
			}

		}

		if(empty($values['j2store_tos'])) {
			$response['msg'] =  JText::_('J2STORE_AGREE_TO_TERMS');
			$response['error'] = '1';
		}

		$dispatcher    =& JDispatcher::getInstance();
		JPluginHelper::importPlugin ('j2store');

		//verify the form data
		$results = array();
		$results = $dispatcher->trigger( "onJ2StoreGetPaymentFormVerify", array( $values['payment_plugin'], $values) );

		for ($i=0; $i<count($results); $i++)
		{
			$result = $results[$i];
			if (!empty($result->error))
			{
				$response['msg'] =  $result->message;
				$response['error'] = '1';
			}

		}

		if($response['error']) {
			$this->setError($response['msg']);
			return false;
		}
		return true;

	}


	/**
	 * This method occurs after payment is attempted,
	 * and fires the onPostPayment plugin event
	 *
	 * @return unknown_type
	 */
	function confirmPayment()
	{
		$app =& JFactory::getApplication();
		$orderpayment_type = JRequest::getVar('orderpayment_type');

		// Get post values
		$values = JRequest::get('post');

		//set the guest mail to null if it is present
		$session = JFactory::getSession();
		$guest_mail = $session->get('guest_mail');

		// get the order_id from the session set by the prePayment
		$orderpayment_id = (int) $app->getUserState( 'j2store.orderpayment_id' );
		if(!$guest_mail) {
			$order_link = 'index.php?option=com_j2store&view=orders&task=view&id='.$orderpayment_id;
		} else {
			$order_link ='';
			//null the guest mail session
			$session->set('guest_mail', '');
		}

		$dispatcher =& JDispatcher::getInstance();
		JPluginHelper::importPlugin ('j2store');

		$html = "";
		$order =& $this->_order;
		$order->load( array('id'=>$orderpayment_id));

		// free product? set the state to confirmed and save the order.
		if ( (!empty($orderpayment_id)) && (float) $order->order_total == (float)'0.00' )
		{
			$order->order_state = trim(JText::_('CONFIRMED'));
			$order->order_state_id = '1'; // PAYMENT RECEIVED.
			if($order->save()) {
				// remove items from cart
				J2StoreHelperCart::removeOrderItems( $order->id );
			}
			//send email
			require_once (JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'helpers'.DS.'orders.php');
			J2StoreOrdersHelper::sendUserEmail($order->user_id, $order->order_id, $order->transaction_status, $order->order_state, $order->order_state_id);

		}
		else
		{
			// get the payment results from the payment plugin
			$results = $dispatcher->trigger( "onJ2StorePostPayment", array( $orderpayment_type, $values ) );

			// Display whatever comes back from Payment Plugin for the onPrePayment
			for ($i=0; $i<count($results); $i++)
			{
				$html .= $results[$i];
			}

			// re-load the order in case the payment plugin updated it
			$order->load( array('id'=>$orderpayment_id) );
		}

		// $order_id would be empty on posts back from Paypal, for example
		if (!empty($orderpayment_id))
		{
			// Set display
			$view = $this->getView( 'checkout', 'html' );
			$view->setLayout('postpayment');
			$view->set( '_doTask', true);
			$view->assign('order_link', $order_link );
			$view->assign('plugin_html', $html);

			// Get and Set Model
			$model = $this->getModel('checkout');
			$view->setModel( $model, true );

			// get the articles to display after checkout
			$articles = array();

			$view->display();
		}
		return;
	}

}
