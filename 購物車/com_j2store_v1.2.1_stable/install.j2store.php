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

jimport('joomla.installer.installer');

// Load K2 language file
$lang = &JFactory::getLanguage();
$lang->load('com_j2store');

$db = & JFactory::getDBO();
$status = new JObject();
$status->modules = array();
$status->plugins = array();
$src = $this->parent->getPath('source');

// install modules

$modules = &$this->manifest->xpath('modules/module');
foreach($modules as $module){
	$mname = $module->getAttribute('module');
	$client = $module->getAttribute('client');
	if(is_null($client)) $client = 'site';
	($client=='administrator')? $path=$src.DS.'administrator'.DS.'modules'.DS.$mname: $path = $src.DS.'modules'.DS.$mname;
	$installer = new JInstaller;
	$result = $installer->install($path);
	$status->modules[] = array('name'=>$mname,'client'=>$client, 'result'=>$result);
}

// install plugins

$plugins = &$this->manifest->xpath('plugins/plugin');
foreach($plugins as $plugin){
	$pname = $plugin->getAttribute('plugin');
	$pgroup = $plugin->getAttribute('group');
	$path = $src.DS.'plugins'.DS.$pgroup;
	$installer = new JInstaller;
	$result = $installer->install($path);
	$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);
	$query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=".$db->Quote($pname)." AND folder=".$db->Quote($pgroup);
	$db->setQuery($query);
	$db->query();
}

// Database modifications [start]

$db = & JFactory::getDBO();


/*
$productfile_fields = $db->getTableColumns('#__j2store_productfiles');
if(!$productfile_fields) {
	$query = "
	CREATE TABLE IF NOT EXISTS `#__j2store_productfiles` (
	`productfile_id` int(11) NOT NULL AUTO_INCREMENT,
	`product_file_display_name` varchar(255) NOT NULL,
	`product_file_save_name` varchar(255) NOT NULL,
	`purchase_required` tinyint(1) NOT NULL,
	`state` tinyint(1) NOT NULL,
	`download_limit` int(11) NOT NULL,
	`product_id` int(11) NOT NULL,
	`ordering` int(11) NOT NULL,
	PRIMARY KEY (`productfile_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
	";
	$db->setQuery($query);
	$db->query();

}

$orderfile_fields = $db->getTableColumns('#__j2store_orderfiles');
if(!$orderfile_fields) {
	$query = "
	CREATE TABLE IF NOT EXISTS `#__j2store_orderfiles` (
	`orderfile_id` int(11) NOT NULL AUTO_INCREMENT,
	`orderitem_id` int(11) NOT NULL,
	`productfile_id` int(11) NOT NULL,
	`limit_count` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	PRIMARY KEY (`orderfile_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;
	";
	$db->setQuery($query);
	$db->query();
}
*/

//end of db modifications
			?>

<?php $rows = 0;?>
<img	src="components/com_j2store/images/j2store-icon.gif" width="109"
	height="48" alt="J2 Store Component" align="right" />
<h2>
	<?php echo JText::_('J2 Store Installation Status'); ?>
</h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'J2 Store'.JText::_('Component'); ?>
			</td>
			<td><strong><?php echo JText::_('Installed'); ?> </strong></td>
		</tr>
		<?php if (count($status->modules)) : ?>
		<tr>
			<th><?php echo JText::_('Module'); ?></th>
			<th><?php echo JText::_('Client'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong><?php echo ($module['result'])?JText::_('Installed'):JText::_('Not installed'); ?>
			</strong></td>
		</tr>
		<?php endforeach;?>
		<?php endif;?>
		<?php if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong><?php echo ($plugin['result'])?JText::_('Installed'):JText::_('Not installed'); ?>
			</strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
