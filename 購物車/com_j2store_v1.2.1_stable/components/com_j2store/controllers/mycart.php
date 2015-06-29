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

class J2StoreControllerMyCart extends JController
{
	public function __construct($config = array())
		{
			parent::__construct($config);
		}

	function display() {

		//initialist system objects
		$app = &JFactory::getApplication();
		$params = &JComponentHelper::getParams('com_j2store');

		//get post vars
		$post = JRequest::get('post');
		if(!empty($post)) {
			//add to cart
			$this->addtocart($post);
		}

		$model = $this->getModel('Mycart');

		//set the state

		// limitstart isn't working for some reason when using getUserStateFromRequest -- cannot go back to page 1
		$limit  = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', '0', 'request', 'int');
		// If limit has been changed, adjust offset accordingly
		$state['limitstart'] = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $state['limit']  	= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');


        $session =& JFactory::getSession();
        $user =& JFactory::getUser();

        $state['filter_user'] = $user->id;
        if (empty($user->id))
        {
            $state['filter_session'] = $session->getId();
        }

        foreach (@$state as $key=>$value)
        {
			$model->setState( $key, $value );
        }

		//load the cart data

		$items = $model->getData();

		$cartobject = $this->checkItems($items, $params->get('show_tax_total'));

		$view = $this->getView( 'mycart', 'html' );
		$view->set( '_view', 'mycart' );
		$view->assign( 'cartobj', $cartobject);
		$view->assign( 'state', $model->getState());
		$view->assign( 'params', $params );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', true);
		$view->setModel( $model, true );
		$view->setLayout( 'default');
		$view->display();

	}

	function ajax() {
		//initialise system objects
		$app = &JFactory::getApplication();
		$params = &JComponentHelper::getParams('com_j2store');

		$elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );

		//convert elements to array
		require_once (JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'helpers'.DS.'_base.php');
		$values = J2StoreHelperBase::elementsToArray( $elements );
		//lets add things to the cart
		$this->addtocart($values);

		$model = $this->getModel('mycart');


		// limitstart isn't working for some reason when using getUserStateFromRequest -- cannot go back to page 1
		$limit  = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', '0', 'request', 'int');
		// If limit has been changed, adjust offset accordingly
		$state['limitstart'] = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $state['limit']  	= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');


        $session =& JFactory::getSession();
        $user =& JFactory::getUser();

        $state['filter_user'] = $user->id;
        if (empty($user->id))
        {
            $state['filter_session'] = $session->getId();
        }

        foreach (@$state as $key=>$value)
        {
			$model->setState( $key, $value );
        }


		$items = $model->getData();

		$cartobject = $this->checkItems($items, $params->get('show_tax_total'));

		$view = $this->getView( 'mycart', 'html' );
		$view->set( '_view', 'mycart' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', true);
		$view->setModel( $model, true );
		$view->assign( 'cartobj', $cartobject);
		$view->assign( 'params', $params );
		//popup layout options
		switch($params->get('popup_style', 1)) {
			case 1:
			default:
				$view->setLayout( 'ajax');
			break;

			case 2:
				$view->setLayout( 'ajaxstatic');
			break;

		}

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
		$app->close();
	}


	function ajaxmini() {
			//initialise system objects
		$app = &JFactory::getApplication();
		$params = &JComponentHelper::getParams('com_j2store');

		$model = $this->getModel('mycart');

        $session =& JFactory::getSession();
        $user =& JFactory::getUser();

        $state['filter_user'] = $user->id;
        if (empty($user->id))
        {
            $state['filter_session'] = $session->getId();
        }

        foreach (@$state as $key=>$value)
        {
			$model->setState( $key, $value );
        }

		$items = $model->getData();

		$cartobject = $this->checkItems($items, $params->get('show_tax_total'));

		$view = $this->getView( 'mycart', 'html' );
		$view->set( '_view', 'mycart' );
		$view->set( '_doTask', true);
		$view->set( 'hidemenu', true);
		$view->setLayout( 'ajaxmini');
		$view->setModel( $model, true );
		$view->assign( 'cartobj', $cartobject);
		$view->assign( 'params', $params );

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
		$app->close();
	}

	public function addtocart($values) {

		if(empty($values)) {
			return;
		}

		//initialise system objects
		$app = &JFactory::getApplication();
		$config = &JComponentHelper::getParams('com_j2store');
		// After login, session_id is changed by Joomla, so store this for reference
		$session = &JFactory::getSession( );
		$session->set( 'old_sessionid', $session->getId( ) );

			$product_id = !empty( $values['product_id'] ) ? ( int ) $values['product_id'] : JRequest::getInt( 'product_id' );
			$product_qty = !empty( $values['product_qty'] ) ? ( int ) $values['product_qty'] : '1';

			//get attributes

			$attributes = array( );
			foreach ( $values as $key => $value )
			{
				if ( substr( $key, 0, 10 ) == 'attribute_' )
				{
					$attributes[] = $value;
				}
			}
			sort( $attributes );
			$attributes_csv = implode( ',', $attributes );


			// Integrity checks on quantity being added
			if ( $product_qty < 0 )
			{
				$product_qty = '1';
			}

			$user = JFactory::getUser( );
			$cart_id = $user->id;
			$id_type = "user_id";
			if ( empty( $user->id ) )
			{
				$cart_id = $session->getId( );
				$id_type = "session";
			}

			// create cart object out of item properties
			$item = new JObject;
			$item->user_id = JFactory::getUser( )->id;
			$item->product_id = ( int ) $product_id;
			$item->product_qty = ( int ) $product_qty;
			$item->product_attributes = $attributes_csv;
			$item->vendor_id = '0'; // TODO: May be useful to create market place site
	
			// add the item to the cart
			JLoader::register( 'J2StoreHelperCart', JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'helpers'.DS.'cart.php');
			$cart_helper = new J2StoreHelperCart();
			$cartitem = $cart_helper->addItem( $item );

			return $cartitem;

	/*
		switch ( $this->getTask())
		{
			case "ajax":

				$this->ajax();
				$app->close();
			break;
			case "redirect":
			default:
			// if a base64_encoded url is present as return, use that as the return url
			// otherwise return == the product view page

				// if a base64_encoded url is present as redirect, redirect there,
				// otherwise redirect to the cart
				$itemid = JRequest::getVar('Itemid');
				$redirect = JRoute::_( "index.php?option=com_j2store&view=mycart&Itemid=" . $itemid, false );
				$this->messagetype = 'message';
				$this->message = JText::_( "Item Added to Your Cart" );
				$this->setRedirect( $redirect, $this->message, $this->messagetype );
			break;
		}
		return;
		*
	*/
	}


	 /**
     *
     * @return unknown_type
     */
    function update()
    {
        $app = &JFactory::getApplication();
        $params = &JComponentHelper::getParams('com_j2store');
        $model 	= $this->getModel('mycart');

        // limitstart isn't working for some reason when using getUserStateFromRequest -- cannot go back to page 1
		$limit  = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', '0', 'request', 'int');
		// If limit has been changed, adjust offset accordingly
		$state['limitstart'] = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $state['limit']  	= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');


        $session =& JFactory::getSession();
        $user =& JFactory::getUser();

        $state['filter_user'] = $user->id;
        if (empty($user->id))
        {
            $state['filter_session'] = $session->getId();
        }

        foreach (@$state as $key=>$value)
        {
			$model->setState( $key, $value );
        }

        $cids = JRequest::getVar('cid', array(0), '', 'ARRAY');
        $product_attributes = JRequest::getVar('product_attributes', array(0), '', 'ARRAY');
        $quantities = JRequest::getVar('quantities', array(0), '', 'ARRAY');
        $post = JRequest::get('post');

        $msg = JText::_('J2STORE_CART_UPDATED');

        $remove = JRequest::getVar('remove');
        if ($remove)
        {
            foreach ($cids as $cart_id=>$product_id)
            {
//            	$keynames = explode('.', $key);
//            	$attributekey = $keynames[0].'.'.$keynames[1];
//            	$index = $keynames[2];
                $row = $model->getTable();

                //main cartitem keys
                $ids = array('user_id'=>$user->id, 'cart_id'=>$cart_id);

				        if (empty($user->id))
		                {
		                    $ids['session_id'] = $session->getId();
		                }

		                if ($return = $row->delete(array('cart_id'=>$cart_id)))
		                {

		                }
            }
        }
        else
        {
            foreach ($quantities as $cart_id=>$value)
            {
                $carts = JTable::getInstance( 'Mycart', 'Table' );
                $carts->load( array( 'cart_id'=>$cart_id) );
                $product_id = $carts->product_id;
                $value = (int) $value;

            	$vals = array();
                $vals['user_id'] = $user->id;
                $vals['session_id'] = $session->getId();
                $vals['product_id'] = $product_id;


              $row = $model->getTable();
              $vals['product_attributes'] = $product_attributes[$cart_id];
              $vals['product_qty'] = $value;
              if (empty($vals['product_qty']) || $vals['product_qty'] < 1)
              {
              	// remove it
              	if ($return = $row->delete($cart_id))
              	{

              	}
              }
              else
              {
                $row->load($cart_id);
              	$row->product_qty = $vals['product_qty'];
              	$row->save();
              }
            }
        }

        JLoader::register( 'J2StoreHelperCart', JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'helpers'.DS.'cart.php');
		$cart_helper = new J2StoreHelperCart();

        if (empty($user->id))
        {
        	$cart_helper->checkIntegrity($session->getId(), 'session_id');
        }
        else
        {
        	$cart_helper->checkIntegrity($user->id);
        }

        $popup = JRequest::getVar('popup');
        if($popup && $remove) {
			$items = $model->getData();

			$cartobject = $this->checkItems($items, $params->get('show_tax_total'));

			$view = $this->getView( 'mycart', 'html' );
			$view->set( '_view', 'mycart' );
			$view->set( '_doTask', true);
			$view->set( 'hidemenu', true);

			if($popup==1) {
				$view->setLayout( 'ajax');
			} else {
				$view->setLayout( 'default');
			}
			$view->setModel( $model, true );
			$view->assign( 'cartobj', $cartobject);
			$view->assign( 'params', $params );
			$view->assign( 'popup', $popup );

			ob_start();
			$view->display();
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;
			$app->close();
		}

        $redirect = JRoute::_( "index.php?option=com_j2store&view=mycart", false );
       	$this->setRedirect( $redirect, $msg);
    }



	 function _setModelState()
    {
        $app = JFactory::getApplication();

        $model = $this->getModel('Mycart');
		$ns = 'com_j2store.mycart';
		$state = array();

		// limitstart isn't working for some reason when using getUserStateFromRequest -- cannot go back to page 1
		$limit  = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', '0', 'request', 'int');
		// If limit has been changed, adjust offset accordingly
		$state['limitstart'] = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $state['limit']  	= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');


        $session =& JFactory::getSession();
        $user =& JFactory::getUser();

        $state['filter_user'] = $user->id;
        if (empty($user->id))
        {
            $state['filter_session'] = $session->getId();
        }

        foreach (@$state as $key=>$value)
        {
            $result = $model->setState( $key, $value );
        }
        return $state;
    }


    /**
     *
     * Method to check config, user group and product state (if recurs).
     * Then get right values accordingly
     * @param array $items - cart items
     * @param boolean - config to show tax or not
     * @return object
     */
    function checkItems( &$items, $show_tax=false)
    {
    	if (empty($items)) { return array(); }


      	$subtotal = 0;
        foreach ($items as $item)
        {

        	if($show_tax)
        	{
        		$cartitem_tax = 0;

		       $product_tax_rate = J2StorePrices::getItemTax($item->product_id);
		       //$product_tax_rate = $taxrate->tax_rate;

		         // track the total tax for this item
		        $cartitem_tax = $product_tax_rate * $item->product_price;

           	 	$item->product_price = $item->product_price + $cartitem_tax;
            	$item->taxtotal = $cartitem_tax;
        	}

        	$item->subtotal = $item->product_price * $item->product_qty;
        	$subtotal = $subtotal + $item->subtotal;
        }
        $cartObj = new JObject();
        $cartObj->items = $items;
    	$cartObj->subtotal = $subtotal;
        return $cartObj;
    }

}
?>
