
ALTER TABLE `#__easyblog_feeds` ADD `item_get_fulltext` tinyint(3) default '0' NOT NULL;
ALTER TABLE `#__easyblog_migrate_content` ADD `filename` VARCHAR( 255 ) NULL;