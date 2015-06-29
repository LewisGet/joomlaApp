<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */


// Check to ensure this file is included in Joomla!
(defined( '_VALID_MOS' ) or defined('_JEXEC')) or die( 'Direct Access to this location is not allowed.' );


function com_install()
{
global $mainframe;
	
	//upgrade the database if needed
	$db  =& JFactory::getDBO();
	
	//upgrade the database if needed
	$db  =& JFactory::getDBO();
	$db->setQuery( "show columns from #__location_fields where `Field`='iscore'");
	$row = $db->loadObject();
	
	if(@$row->Field != 'iscore'){
		$db  =& JFactory::getDBO();
		$db->setQuery( "ALTER TABLE #__location_fields add `iscore` int(11);");
		$db->Query();	
		//if they didnt have iscore, all fields will be 1
		$db->setQuery( "UPDATE #__location_fields SET `iscore`= 1;");
		$db->Query();			
	}	
	
	
	//update the database
	$db  =& JFactory::getDBO();
	$db->setQuery( "show columns from #__location_fields where `Field`='user_field'");
	$row = $db->loadObject();
	
	if(@$row->Field != 'user_field'){
		$db  =& JFactory::getDBO();
		$db->setQuery( "alter table #__location_fields add column `user_field` int(11) NULL");
		
		$db->Query();	
		
		$db->setQuery( "UPDATE #__location_fields SET `user_field`=1");
		$db->Query();	
	}
	
		//update the database
	$db  =& JFactory::getDBO();
	$db->setQuery( "show columns from #__location_fields where `Field`='visitor_field'");
	$row = $db->loadObject();
	
	if(@$row->Field != 'visitor_field'){
		$db  =& JFactory::getDBO();
		$db->setQuery( "alter table #__location_fields add column `visitor_field` int(11) NULL");
		
		$db->Query();	
		
		$db->setQuery( "UPDATE #__location_fields SET `visitor_field`=1");
		$db->Query();	
	}
	
	
	
	
	$db->setQuery( "SELECT count(*) as cnt FROM #__location_fields where iscore = 1");
	$count =(int) $db->loadResult();

	//ensure we have the required system fields.  Older versions will have count = 8 (before link & email)
	if($count != 14){
	
		$sql = "DELETE FROM `#__location_fields` where iscore = 1;"; $db->setQuery($sql); $db->Query();
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (1,'Address','text',1,1,1,1);"; $db->setQuery($sql); $db->Query();
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (2,'Address 2','text',2,1,1,1);"; $db->setQuery($sql); $db->Query();
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (3,'City','text',3,1,1,1);"; $db->setQuery($sql); $db->Query();
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (4,'State','text',4,1,1,1);"; $db->setQuery($sql); $db->Query();
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (5,'PostalCode','text',5,1,1,1);"; $db->setQuery($sql); $db->Query();
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (6,'Phone','text',6,1,1,1);"; $db->setQuery($sql); $db->Query();
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (7,'Date','Date',7,1,1,1);"; $db->setQuery($sql); $db->Query();
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (8,'Country','text',8,1,1,1);"; $db->setQuery($sql); $db->Query();
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (9,'Link','link',9,1,1,1);"; $db->setQuery($sql); $db->Query();
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (10,'Email','email',10,1,1,1);"; $db->setQuery($sql); $db->Query();
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (11,'Image','image',11,1,1,0);"; $db->setQuery($sql); $db->Query();			
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (12,'meta-description','meta',12,1,1,0);"; $db->setQuery($sql); $db->Query();	
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (13,'meta-keywords','meta',13,1,1,0);"; $db->setQuery($sql); $db->Query();	
		$sql = "insert  into `#__location_fields`(`id`,`name`,`type`,`order`,`published`,`iscore`,`user_field`) values (14,'TLD','TLD',14,1,1,0);"; $db->setQuery($sql); $db->Query();
							
	}
	
	$db  =& JFactory::getDBO();
	$db->setQuery( "UPDATE #__location_fields SET user_field=0 WHERE id in (11,12,13,14) ");
	$db->Query();	
	
	//upgrade the database if needed
	$db  =& JFactory::getDBO();
	$db->setQuery( "SELECT id FROM #__location_tags WHERE `order` is null or `order`=0");
	$rows = $db->loadObjectList();
	
	//set an ordering if there are any missing
	$i = 1;
	foreach ($rows as $row){
		$db->setQuery( "UPDATE #__location_tags SET `order` = $i WHERE `id`={$row->id}");
		$db->Query();
		$i++;		
	}
	
	//upgrade the database if needed
	$db  =& JFactory::getDBO();
	$db->setQuery( "show columns from #__locations where `Field`='user_id'");
	$row = $db->loadObject();
	
	if(@$row->Field != 'user_id'){
		$db  =& JFactory::getDBO();
		$db->setQuery( "ALTER TABLE #__locations add `user_id` int(11);");
		$db->Query();	
	}
	
	
	//upgrade the database if needed
	$db  =& JFactory::getDBO();
	$db->setQuery( "show columns from #__location_zips where `Field`='updated'");
	$row = $db->loadObject();
	
	if(@$row->Field != 'user_id'){
		$db  =& JFactory::getDBO();
		$db->setQuery( "ALTER TABLE #__location_zips add `updated` datetime;");
		$db->Query();	
	}

	//upgrade the database if needed
	$db  =& JFactory::getDBO();
	$db->setQuery( "show columns from #__location_tags where `Field`='marker_shadow'");
	$row = $db->loadObject();
	
	if(@$row->Field != 'marker_shadow'){
		$db  =& JFactory::getDBO();
		$db->setQuery( "ALTER TABLE #__location_tags add `marker_shadow` varchar(255);");
		$db->Query();	
	}

	//upgrade the database if needed
	$db  =& JFactory::getDBO();
	$db->setQuery( "show columns from #__location_zips where `Field`='country'");
	$row = $db->loadObject();
	
	if(@$row->Field != 'country'){
		$db  =& JFactory::getDBO();
		$db->setQuery( "ALTER TABLE #__location_zips add `country` varchar(255);");
		$db->Query();	
	}
	
	//update the database
	$db  =& JFactory::getDBO();
	$db->setQuery( "show columns from #__location_tags where `Field`='user_tag'");
	$row = $db->loadObject();
	
	if(@$row->Field != 'user_tag'){
		$db  =& JFactory::getDBO();
		$db->setQuery( "alter table #__location_tags add column `user_tag` int(11) NULL");
		$db->Query();	
	}
	
	

	//update the database
	$db  =& JFactory::getDBO();
	$db->setQuery( "show columns from #__location_tags where `Field`='tag_group'");
	$row = $db->loadObject();
	
	if(@$row->Field != 'tag_group'){
		$db  =& JFactory::getDBO();
		$db->setQuery( "alter table #__location_tags add column `tag_group` varchar(255);");
		$db->Query();	
	}


	//update the database
	$db  =& JFactory::getDBO();
	$db->setQuery( "show columns from #__location_tags where `Field`='child_of'");
	$row = $db->loadObject();
	
	if(@$row->Field != 'child_of'){
		$db  =& JFactory::getDBO();
		$db->setQuery( "alter table #__location_tags add column `child_of` varchar(255);");
		$db->Query();	
	}

		
	//update the database
	$db  =& JFactory::getDBO();
	$db->setQuery( "show columns from #__location_tags where `Field`='tag_group_order'");
	$row = $db->loadObject();
	
	if(@$row->Field != 'tag_group_order'){
		$db  =& JFactory::getDBO();
		$db->setQuery( "alter table #__location_tags add column `tag_group_order` int(11) NULL");
		$db->Query();	
	}
	
	
	//upgrade the database if needed
	$db  =& JFactory::getDBO();
	$db->setQuery( "show columns from #__location_zips where `Field`='location_id'");
	$row = $db->loadObject();
	
	if(@$row->Field != 'location_id'){

		$db  =& JFactory::getDBO();
		$db->setQuery( "ALTER TABLE #__location_zips add `location_id` int(11) NOT NULL;");
		$db->Query();	

		$db->setQuery( "ALTER TABLE #__locations drop column zip");
		$db->Query();

		$db->setQuery( "ALTER TABLE #__locations drop column lat");
		$db->Query();

		$db->setQuery( "ALTER TABLE #__locations drop column lng");
		$db->Query();	

	}

}
?>
