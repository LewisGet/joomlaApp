<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$doc =& JFactory::getDocument();

if(JRequest::getString('layout','') == 'mobile'){
	$doc->addStyleSheet( 'components/com_locator/assets/locator_mobile.css' );
}else{
	$doc->addStyleSheet( 'components/com_locator/assets/locator.css' );
}

//ML
$external_css = $params->get('external_css','');
$external_font = $params->get('external_font','');
$cssref = $params->get('cssref','');
$doc =& JFactory::getDocument();

if(strlen($external_css) > 0){	
	$doc->addStyleSheet( '/components/com_locator/assets/css/' . $external_css);
}

if(strlen($cssref) > 0){	
	$doc->addStyleSheet($cssref);
}

if(strlen($external_font) > 0){	
	$doc->addStyleDeclaration( 'body{	font-family:' . $external_font . ';}');
}

$form_template = $params->get('form_template','{zipform}{keywordform}{tagdropdown}{taggroups}{tagcheckboxes}{statedropdown}{citydropdown}{countrydropdown}');

?><div class="com_locator_forms">
	<?php 
	
	if($params->get('article',0) > 0 ){
		$this->showArticle($params);
	}
	
	//////////////////////////////////////////
	$buffer = '';
	if($params->get('showzipform') == 1){
				
		ob_start();
		require('components' .DS. 'com_locator' .DS. 'views' .DS. 'directory' .DS. 'tmpl' .DS. 'zip_form.php'); 
		$buffer = ob_get_contents();
		ob_end_clean();
	} 
	
	$form_template = str_replace('{zipform}',$buffer,$form_template);
	
	//////////////////////////////////////////
	$buffer = '';
	if($params->get('showkeywordform') == 1){
		
		ob_start();
		require('components' .DS. 'com_locator' .DS. 'views' .DS. 'directory' .DS. 'tmpl' .DS. 'keyword_form.php'); 
		$buffer = ob_get_contents();
		ob_end_clean();
	}
	$form_template = str_replace('{keywordform}',$buffer,$form_template);
	
	//////////////////////////////////////////
	$buffer = '';
	if($params->get('showtagdropdown') == 1){
			  
		$model = new LocatorModelDirectory; 
		$model->getTagList();
		
		$this->lists['tags'] = $model->_lists['tags'];
		ob_start();
		require('components' .DS. 'com_locator' .DS. 'views' .DS. 'directory' .DS. 'tmpl' .DS. 'tag_form.php'); 
		$buffer = ob_get_contents();
		ob_end_clean();
	}
	$form_template = str_replace('{tagdropdown}',$buffer,$form_template);
	
	
	//////////////////////////////////////////
	$buffer = '';
	if($params->get('showtaggroups',0) == 1){
		
		$model = new LocatorModelDirectory; 
		$model->getTagGroupLists();
		
		$this->lists['tag_groups'] = $model->_lists['tag_groups'];
		ob_start();		
		require('components' .DS. 'com_locator' .DS. 'views' .DS. 'directory' .DS. 'tmpl' .DS. 'tag_group_form.php'); 
		$buffer = ob_get_contents();
		ob_end_clean();
	}
	$form_template = str_replace('{taggroups}',$buffer,$form_template);

	//////////////////////////////////////////
	$buffer = '';
	if($params->get('showtagcheckboxes',0) == 1){
		
		$model = new LocatorModelDirectory; 
		$model->getTagGroupCheckBoxes();
		
		$this->lists['tag_groups_checkboxes'] = $model->_lists['tag_groups_checkboxes'];
		ob_start();		
		require('components' .DS. 'com_locator' .DS. 'views' .DS. 'directory' .DS. 'tmpl' .DS. 'tag_cb_form.php'); 
		$buffer = ob_get_contents();
		ob_end_clean();
	}
	$form_template = str_replace('{tagcheckboxes}',$buffer,$form_template);
		
	
	//////////////////////////////////////////
	$buffer = '';
	if($params->get('showstatedropdown') == 1){
		
		if($params->get('showcitydropdown',0) == 1){

			$model = new LocatorModelDirectory; 
			
			$model->showCityList(false);
		}
		
		$model = new LocatorModelDirectory; 
		$model->getProvinceList(false);
		$this->lists['states'] = $model->_lists['states'];
		ob_start();		
		require('components' .DS. 'com_locator' .DS. 'views' .DS. 'directory' .DS. 'tmpl' .DS. 'state_form.php'); 
		$buffer = ob_get_contents();
		ob_end_clean();
	}
	$form_template = str_replace('{statedropdown}',$buffer,$form_template);
	
		
	//////////////////////////////////////////
	$buffer = '';
	if($params->get('showcitydropdown',0) == 1){
		
		$model = new LocatorModelDirectory; 
		$model->getCityList();
		$this->lists['city'] = $model->_lists['city'];
		ob_start();		
		require('components' .DS. 'com_locator' .DS. 'views' .DS. 'directory' .DS. 'tmpl' .DS. 'city_form.php'); 
		$buffer = ob_get_contents();
		ob_end_clean();
	}
	$form_template = str_replace('{citydropdown}',$buffer,$form_template);
		
	
	//////////////////////////////////////////
	$buffer = '';
	if($params->get('showcountrydropdown',0) == 1){
		
		if($params->get('showcitydropdown',0) == 1){

			$model = new LocatorModelDirectory; 
			
			$model->showCityListByCountry(false);
			
		}
		
		$model = new LocatorModelDirectory; 
		$model->getCountryList();
		$this->lists['country'] = $model->_lists['country'];
		
		ob_start();		
		require('components' .DS. 'com_locator' .DS. 'views' .DS. 'directory' .DS. 'tmpl' .DS. 'country_form.php'); 
		$buffer = ob_get_contents();
		ob_end_clean();
	}
	$form_template = str_replace('{countrydropdown}',$buffer,$form_template);
	
	echo $form_template;
	
	require('components' .DS. 'com_locator' .DS. 'views' .DS. 'directory' .DS. 'tmpl' .DS. 'form_footer.php'); 
		  
	?>
</div>


