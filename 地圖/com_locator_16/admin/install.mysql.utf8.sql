		
	CREATE TABLE IF NOT EXISTS `#__location_fields` (
	  `id` int(11) NOT NULL auto_increment,
	  `name` varchar(255) character set utf8 default NULL,
	  `type` varchar(255) character set utf8 default NULL,
	  `order` int(11) default NULL,
	  `iscore` int(11) default NULL, 
	  `published` int(11) default NULL,
	  `visitor_field` int(11) default NULL,
	  PRIMARY KEY  (`id`),
	  KEY `name` (`name`(255)),
	  KEY `published` (`published`),
	  KEY `order` (`order`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
	
	CREATE TABLE IF NOT EXISTS `#__location_fields_link` (
	  `id` int(11) NOT NULL auto_increment,
	  `location_fields_id` int(11) default NULL,
	  `location_id` int(11) default NULL,
	  `value` varchar(5000) character set utf8 default NULL,
	  PRIMARY KEY  (`id`),
	  KEY `location_id` (`location_id`),
	  KEY `value` (`value`(333)),
	  KEY `location_id_location_fields_id` (`location_fields_id`,`location_id`),
	  KEY `location_fields_id` (`location_fields_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
	
		
	CREATE TABLE  IF NOT EXISTS `#__location_tags` (
	  `id` int(11) NOT NULL auto_increment,
	  `name` varchar(255) character set utf8 default NULL,
	  `order` int(11) default NULL,
	  `category` varchar(255) character set utf8 default NULL,
	  `marker` varchar(255) character set utf8 default NULL,
	  `marker_shadow` varchar(255) character set utf8 default NULL,
	  `description` text character set utf8,
	  `tag_group` varchar(255) character set utf8 default NULL,
	  `child_of` varchar(255) character set utf8 default NULL,
	  `tag_group_order` int(11) default NULL,
	  PRIMARY KEY  (`id`),
	  KEY `name` (`name`(255)),
	  KEY `category` (`category`(255)),
	  KEY `order` (`order`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
	
	
	CREATE TABLE IF NOT EXISTS `#__location_tags_link` (
	  `id` int(11) NOT NULL auto_increment,
	  `tag_id` int(11) default NULL,
	  `location_id` int(11) default NULL,
	  `value` varchar(255) character set utf8 default NULL,
	  PRIMARY KEY  (`id`),
	  KEY `tag_id` (`tag_id`),
	  KEY `location_id_tag_id` (`tag_id`,`location_id`),
	  KEY `location_id` (`location_id`),
	  KEY `value` (`value`(255))
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
	
	
	CREATE TABLE IF NOT EXISTS `#__location_zips` (
	  `zip` varchar(255) character set utf8 NOT NULL default '',
	  `state` char(2) character set utf8 default NULL,
	  `lng` double default NULL,
	  `lat` double default NULL,
	  `location_id` int(11) default NULL,
	  `country` varchar(255) character set utf8 default NULL,
	   UNIQUE KEY `location_id` (`location_id`),
	   KEY `country` (`country`),
	   KEY `zip` (`zip`),
	   KEY `lng` (`lng`),
	   KEY `lat` (`lat`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	
	
	CREATE TABLE IF NOT EXISTS `#__locations` (
	  `id` int(11) NOT NULL auto_increment,
	  `name` varchar(500) character set utf8 default NULL,
	  `description` text character set utf8,
	  `published` int(11) default NULL,
	  `user_id` int(11) default NULL,
	  PRIMARY KEY  (`id`),
	  KEY `name` (`name`(255)),
	  KEY `user_id` (`user_id`),
	  KEY `published` (`published`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
