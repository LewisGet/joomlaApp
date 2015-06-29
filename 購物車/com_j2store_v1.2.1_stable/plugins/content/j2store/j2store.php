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


defined('JPATH_BASE') or die;

jimport('joomla.utilities.date');

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'library'.DS.'prices.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'helpers'.DS.'cart.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_j2store'.DS.'helpers'.DS.'downloads.php');

class plgContentJ2store extends JPlugin
{

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	function onContentAfterDisplay($option, $item, $params)
	{
		$mainframe = &JFactory::getApplication();

		$opt = substr($option,0,11);
		$lang = JFactory::getLanguage();
		$lang->load('com_j2store');
		$j2params = &JComponentHelper::getParams('com_j2store');
		if(!empty($item->id) && $opt=='com_content'){
			$item_price = J2StorePrices::getItemPrice($item->id);
			$product_enabled = J2StorePrices::getItemEnabled($item->id);
		}
			
		//if ($item_price > 0) {
		if (!$j2params->get('show_addtocart') && $product_enabled == 1 ) {

			$doc = &JFactory::getDocument();
			// $doc->addStyleSheet(JURI::base().'components'.DS.'com_j2store'.DS.'css'.DS.'style.css');
			// show/hide add to cart button
			$content = '';
			$content = J2StoreHelperCart::getAjaxCart($item, $item_price);

			$output = $content;

			if($j2params->get('isregister')) {
				$user = JFactory::getUser();
				if($user->id && !$j2params->get('show_addtocart')) {
					$isregistered = true;
					$output = $content;
				} else {
					$isregistered = false;
					$output = '';
				}
			}

		} else {
			$output = '';

		}

		//free file attachments
		$freefiles = J2StoreDownloads::getDownloadHtml($item->id);
		$output = $freefiles.$output;

		return $output;
	}

	function onContentPrepareForm($form, $data)
	{

		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}

		// Check we are manipulating a valid form.
		$name = $form->getName();

		if (!in_array($name, array('com_content.article'))) {
			return true;
		}

		$doc = &JFactory::getDocument();
		$doc->addStyleSheet(JURI::base().'components'.DS.'com_j2store'.DS.'css'.DS.'style.css');

		// Add the registration fields to the form.
		JForm::addFormPath(dirname(__FILE__).'/j2store');
		JForm::addFieldPath(dirname(__FILE__).'/j2store/fields');
		$form->loadFile('j2store', false);

		// Load the data from j2store_prices table into the form
		$articleId = isset($data->id) ? $data->id : 0;

		// Load the price data from the database.
		$db = JFactory::getDbo();
		$db->setQuery(
				'SELECT article_id,item_price,item_tax,item_shipping,item_sku,product_enabled FROM #__j2store_prices' .
				' WHERE article_id = '.(int) $articleId);
		$price = $db->loadObject();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			$this->_subject->setError($db->getErrorMsg());
			return false;
		}

		if( isset($price) )
		{
			$data->attribs['product_enabled']=$price->product_enabled;
			$data->attribs['item_price']=$price->item_price;
			$data->attribs['item_tax']=$price->item_tax;
			$data->attribs['item_shipping']=$price->item_shipping;
			$data->attribs['item_sku']=$price->item_sku;
		}
		return true;
	}

	/**
	 * Example after save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		The context of the content passed to the plugin (added in 1.6)
	 * @param	object		A JTableContent object
	 * @param	bool		If the content is just about to be created
	 *
	 */

	function onContentAfterSave($context, &$data, $isNew)
	{
		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_content.article'))) {
			return true;
		}

		$articleId = isset($data->id) ? $data->id : 0;
		// convert the joomla article attributes from json to object
		$attribs = json_decode($data->attribs)	;

		$db = JFactory::getDbo();
		$db->setQuery('SELECT COUNT(*) FROM #__j2store_prices WHERE article_id = '.$articleId);
		$res = $db->loadResult();
		if( !(empty($attribs->item_tax)
				&&empty($attribs->item_price)
				&&empty($attribs->item_shipping)
				&&empty($attribs->item_sku)
				&&empty($attribs->product_enabled)	) ){
			$res++;
		}
		
		if($res==2){
			//update query
			$db = JFactory::getDbo();
			$db->setQuery('UPDATE #__j2store_prices SET item_tax='.$attribs->item_tax
					.',item_price='.$attribs->item_price
					.',item_shipping='.$attribs->item_shipping
					.',item_sku="'.$attribs->item_sku.'"'
					.',product_enabled='.$attribs->product_enabled
					.' WHERE article_id = '.$articleId);

			if (!$db->query()) {
				throw new Exception($db->getErrorMsg());
			}

		}else{

			// check and allow entry in price table only if product enabled, chosen
			if($attribs->product_enabled ==1){
				//insert query
				JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j2store'.DS.'tables');
				$row = JTable::getInstance('prices','Table');

				$row->item_tax=$attribs->item_tax;
				$row->item_price=$attribs->item_price;
				$row->item_shipping=$attribs->item_shipping;
				$row->item_sku=$attribs->item_sku;
				$row->product_enabled=$attribs->product_enabled;
				$row->article_id=$articleId;
					
				if ( !$row->save() )
				{
					$messagetype = 'notice';
					$message = JText::_( 'Prices Save Failed' )." - ".$row->getError();
				}
			}

		}

		return true;
	}

	/**
	 * Remove all item price information for the given article ID from j2store-price table
	 *
	 * Method is called before article data is deleted from the database
	 *
	 * @param	string	The context for the content passed to the plugin.
	 * @param	object	The data relating to the content that was deleted.
	 */
	function onContentAfterDelete($context, $data)
	{
		$articleId = isset($data->id) ? $data->id : 0;

		//$articleId	= JArrayHelper::getValue($data, 'id', 0, 'int');

		if ($articleId)
		{
			try
			{
				$db = JFactory::getDbo();
				$db->setQuery('DELETE FROM #__j2store_prices WHERE article_id = '.$articleId );

				if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
			}
			catch (JException $e)
			{
				$this->_subject->setError($e->getMessage());
				return false;
			}
		}
		return true;
	}
}
