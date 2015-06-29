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
//jimport('joomla.database.table.content');

class J2StoreControllerProducts extends JController {

	function __construct()
	{
		parent::__construct();

	}


	function createattribute() {
		$model  = $this->getModel( 'productattributes' );
		$row = $model->getTable();
		$row->product_id = JRequest::getVar( 'id' );
		$row->productattribute_name = JRequest::getVar( 'productattribute_name' );
		$row->ordering = '99';
		//  $post=JRequest::get('post');
			
		if ( !$row->save() )
		{
			$messagetype = 'notice';
			$message = JText::_( 'Save Failed' )." - ".$row->getError();
		}

		$redirect = "index.php?option=com_j2store&view=products&task=setattributes&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $message, $messagetype );

	}


	function setattributes()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('productattributes');
		$ns = 'com_j2store.productattributes';

		$filter_order		= $app->getUserStateFromRequest( $ns.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $ns.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$id = JRequest::getVar('id', 0, 'get', 'int');

		//JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'tables');
		$row = JTable::getInstance('content','JTable');
		$row->load($id);

		$items = $model->getData();
		$total		= & $model->getTotal();
		$pagination = & $model->getPagination();

		$view   = $this->getView( 'productattributes', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_j2store&view=products&task=setattributes&tmpl=component&id=".$id);
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->assign( 'items', $items );
		$view->assign( 'total', $total );
		$view->assign( 'lists', $lists );
		$view->assign( 'pagination', $pagination );
		$view->assign( 'product_id', $id );
		$view->setLayout( 'default' );
		$view->display();
	}


	function saveattributes()
	{
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';

		$model = $this->getModel('productattributes');
		$row = $model->getTable();

		$id = JRequest::getVar('id', 0, 'get', 'int');
		$cids = JRequest::getVar('cid', array(0), 'request', 'array');
		$name = JRequest::getVar('name', array(0), 'request', 'array');
		$ordering = JRequest::getVar('ordering', array(0), 'request', 'array');

		foreach (@$cids as $cid)
		{
			$row->load( $cid );
			$row->productattribute_name = $name[$cid];
			$row->ordering = $ordering[$cid];

			if (!$row->check() || !$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		$row->reorder();

		if ($error)
		{
			$this->message = JText::_('Error') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = "index.php?option=com_j2store&view=products&task=setattributes&id={$id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}


	function deleteattributes()
	{
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$product_id = JRequest::getVar( 'product_id' );
		if (!isset($this->redirect)) {
			$this->redirect = JRequest::getVar( 'return' )
			? base64_decode( JRequest::getVar( 'return' ) )
			: 'index.php?option=com_j2store&view=products&task=setattributes&id='.$product_id.'&tmpl=component';
			$this->redirect = JRoute::_( $this->redirect, false );
		}

		$model = $this->getModel('productattributes');
		$row = $model->getTable();

		$cids = JRequest::getVar('cid', array (0), 'request', 'array');
		foreach (@$cids as $cid)
		{
			if (!$row->delete($cid))
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		if ($error)
		{
			$this->message = JText::_('Error') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('Items Deleted');
		}

		$this->setRedirect( $this->redirect, $this->message, $this->messagetype );
	}

	function setattributeoptions()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('productattributeoptions');
		$ns = 'com_j2store.productattributeoptions';
		$filter_order		= $app->getUserStateFromRequest( $ns.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $ns.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$id = JRequest::getVar('id', 0, 'get', 'int');

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'tables');
		$row = JTable::getInstance('ProductAttributes', 'Table');
		$row->load($model->getId());

		$items = $model->getData();
		$total		= & $model->getTotal();
		$pagination = & $model->getPagination();

		$view   = $this->getView( 'productattributeoptions', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_j2store&view=products&task=setattributeoptions&tmpl=component&id=".$model->getId());
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->assign( 'items', $items );
		$view->assign( 'total', $total );
		$view->assign( 'lists', $lists );
		$view->assign( 'pagination', $pagination );
		$view->setLayout( 'default' );
		$view->display();
	}


	function createattributeoption()
	{
		$model  = $this->getModel( 'productattributeoptions' );
		$row = $model->getTable();
		$row->productattribute_id = JRequest::getVar( 'id' );
		$row->productattributeoption_name = JRequest::getVar( 'productattributeoption_name' );
		$row->productattributeoption_price = JRequest::getVar( 'productattributeoption_price' );
		$row->productattributeoption_code = JRequest::getVar( 'productattributeoption_code' );
		$row->productattributeoption_prefix = JRequest::getVar( 'productattributeoption_prefix' );
		$row->ordering = '99';

		if (!$row->save() )
		{
			$this->messagetype  = 'notice';
			$this->message      = JText::_( 'Save Failed' )." - ".$row->getError();
		}

		$redirect = "index.php?option=com_j2store&view=products&task=setattributeoptions&id={$row->productattribute_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}


	function saveattributeoptions()
	{
		$error = false;
		$this->messagetype  = '';
		$this->message      = '';

		$model = $this->getModel('productattributeoptions');
		$row = $model->getTable();

		$productattribute_id = JRequest::getVar('id');
		$cids = JRequest::getVar('cid', array(0), 'request', 'array');
		$name = JRequest::getVar('name', array(0), 'request', 'array');
		$prefix = JRequest::getVar('prefix', array(0), 'request', 'array');
		$price = JRequest::getVar('price', array(0), 'request', 'array');
		$code = JRequest::getVar('code', array(0), 'request', 'array');
		$ordering = JRequest::getVar('ordering', array(0), 'request', 'array');

		foreach (@$cids as $cid)
		{
			$row->load( $cid );
			$row->productattributeoption_name = $name[$cid];
			$row->productattributeoption_prefix = $prefix[$cid];
			$row->productattributeoption_price = $price[$cid];
			$row->productattributeoption_code = $code[$cid];
			$row->ordering = $ordering[$cid];

			if (!$row->check() || !$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		$row->reorder();

		if ($error)
		{
			$this->message = JText::_('Error') . " - " . $this->message;
		}
		else
		{
			$this->message = "";
		}

		$redirect = "index.php?option=com_j2store&view=products&task=setattributeoptions&id={$productattribute_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	function deleteattributeoptions()
	{
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$productattribute_id = JRequest::getVar( 'pa_id' );
		if (!isset($this->redirect)) {
			$this->redirect = JRequest::getVar( 'return' )
			? base64_decode( JRequest::getVar( 'return' ) )
			: 'index.php?option=com_j2store&view=products&task=setattributeoptions&id='.$productattribute_id.'&tmpl=component';
			$this->redirect = JRoute::_( $this->redirect, false );
		}

		$model = $this->getModel('productattributeoptions');
		$row = $model->getTable();

		$cids = JRequest::getVar('cid', array (0), 'request', 'array');
		foreach (@$cids as $cid)
		{
			if (!$row->delete($cid))
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		if ($error)
		{
			$this->message = JText::_('Error') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('Items Deleted');
		}

		$this->setRedirect( $this->redirect, $this->message, $this->messagetype );
	}

	/*
	 *  product files
	 * */

	function setfiles()
	{

		$app = JFactory::getApplication();
		$model = $this->getModel('productfiles');
		$context = 'com_j2store.productfiles';
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
			
		$total		= $model->getTotal();
		$pagination = $model->getPagination();

		//$id = JRequest::getVar('id', 0, 'get', 'int');
		$id = JRequest::getVar('id');

		//set states
		$model->setState('product.id',$id);

		// get items from the table
		$items = $model->getItems();
		$row = JTable::getInstance('content','JTable');
		$row->load($id);

		$files	 = $model->getFiles();
		$error = $model->getError();

		$view   = $this->getView( 'productfiles', 'html' );
		$view->set( '_controller', 'products' );
		$view->set( '_view', 'products' );
		$view->set( '_action', "index.php?option=com_j2store&view=products&task=setfiles&tmpl=component&id=".$id);
		$view->setModel( $model, true );
		$view->assign( 'state', $model->getState() );
		$view->assign( 'row', $row );
		$view->assign( 'items', $items );
		$view->assign( 'files', $files );
		$view->assign( 'lists', $lists ); // for pagination (footer)
		$view->assign( 'error', $error);
		$view->assign( 'total', $total);
		$view->assign( 'pagination', $pagination);
		$view->assign( 'product_id', $id );
		//$view->assign( 'product_id', $id );

		$view->setLayout( 'default' );
		$view->display();
	}

	function savefiles()
	{

		$error = false;
		$this->messagetype  = '';
		$this->message      = '';

		$model = $this->getModel('productfiles');
		$row = $model->getTable();

		$id = JRequest::getVar('id', 0, 'get', 'int');
		$cids = JRequest::getVar('cid', array(0), 'request', 'array');
		$file_disp_name = JRequest::getVar('product_file_display_name', array(0), 'request', 'array');
		$purchase_required = JRequest::getVar('product_file_purchase_required', array(0), 'request', 'array');
		$state = JRequest::getVar('product_file_state', array(0), 'request', 'array');
		$download_limit = JRequest::getVar('product_file_download_limit', array(0), 'request', 'array');
		$ordering = JRequest::getVar('product_file_ordering', array(0), 'request', 'array');

		foreach ($cids as $cid)
		{
			$row->load( $cid );
			$row->product_file_display_name = $file_disp_name[$cid];
			$row->purchase_required = $purchase_required[$cid];
			$row->state = $state[$cid];
			$row->download_limit = $download_limit[$cid];
			$row->ordering = $ordering[$cid];

			if (!$row->check() || !$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		$row->reorder();

		if ($error)
		{
			$this->message = JText::_('Error') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_("Changes saved successfully");
		}

		$redirect = "index.php?option=com_j2store&view=products&task=setfiles&id=".$id."&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}


	function createfile() {

		$model  = $this->getModel( 'productfiles' );
		$row = $model->getTable();
		$row->product_id = JRequest::getVar( 'id' );
		$id=$row->product_id ;
		$file = JRequest::getVar('savename', array(), 'files', 'array');

		//get display name or use he file's original name
		$display_file_name = JRequest::getVar( 'displayname' );
		$row->product_file_save_name = $file_name = $file['name'];
		$row->product_file_display_name = (!empty($display_file_name))?$display_file_name: $file_name;
		$row->purchase_required = JRequest::getVar( 'purchase_required' );
		$row->state = JRequest::getVar( 'state' );
		$row->download_limit = JRequest::getVar( 'download_limit' );
		$row->ordering = '99';

		//  $post=JRequest::get('post');
		if (!$model->saveFile($file)){
			$messagetype = 'notice';
			$message = JText::_( 'File Save Failed' )." - ".$model->getError();
		} else {

			if ( !$row->save())
			{
				$messagetype = 'notice';
				$message = JText::_( 'Save Failed' )." - ".$row->getError();
			}

		}

		$redirect = "index.php?option=com_j2store&view=products&task=setfiles&id={$row->product_id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );

		$this->setRedirect( $redirect, $message, $messagetype );

	}

	function deletefile()
	{
		$app = JFactory::getApplication();
		$model = $this->getModel('productfiles');
		$user	= JFactory::getUser();

		// Deletes the selected rows from the table
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$id = JRequest::getVar( 'id' );
		if (!isset($this->redirect)) {
			$this->redirect = JRequest::getVar( 'return' )
			? base64_decode( JRequest::getVar( 'return' ) )
			: 'index.php?option=com_j2store&view=products&task=setfiles&id='.$id.'&tmpl=component';
			$this->redirect = JRoute::_( $this->redirect, false );
		}

		$model = $this->getModel('productfiles');
		$row = $model->getTable();

		$cids = JRequest::getVar('cid', array (0), 'request', 'array');

		foreach (@$cids as $cid)
		{
			//code to delete the file from disk	 (call to model)
			//get the image name and call delete from model

			if($row->load($cid))
			$file_name = $row->product_file_save_name;

			if ( ! $model->deleteFile($file_name) )
			{
				$this->message = JText::_( 'Delete Failed' )." - ".$model->getError();
				$this->messagetype = 'error';
			}

			if (!$row->delete($cid))
			{
				$this->message .= $row->getError();
				$this->messagetype = 'error';
				$error = true;
			}
		}

		if ($error)
		{
			$this->message = JText::_('Error') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('Items Deleted');
			$this->messagetype = 'notice';
		}


		$redirect = "index.php?option=com_j2store&view=products&task=setfiles&id={$id}&tmpl=component";
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

}
