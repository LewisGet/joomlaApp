CREATE TABLE IF NOT EXISTS `#__easyblog_category_acl` (
  `id` bigint(20) NOT NULL auto_increment,
  `category_id` bigint(20) NOT NULL,
  `acl_id` bigint(20) NOT NULL,
  `type` varchar(255) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `status` tinyint(1) default 0,
  PRIMARY KEY  (`id`),
  KEY `easyblog_category_acl` (`category_id`),
  KEY `easyblog_category_acl_id` (`acl_id`),
  KEY `easyblog_content_type` (`content_id`, `type`),
  KEY `easyblog_category_content_type` (`category_id`, `content_id`, `type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__easyblog_category_acl_item` (
  `id` bigint(20) NOT NULL auto_increment,
  `action` varchar(255) NOT NULL,
  `description` text null,
  `published` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `default` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

insert into `#__easyblog_category_acl_item` (`id`, `action`, `description`, `published`, `default`) values ('1', 'view', 'can view the category blog posts.', 1, 1);
insert into `#__easyblog_category_acl_item` (`id`, `action`, `description`, `published`, `default`) values ('2', 'select', 'can select the category during blog post creation', 1, 1);
ALTER TABLE `#__easyblog_post` ADD COLUMN `latitude` VARCHAR(255) NULL;
ALTER TABLE `#__easyblog_post` ADD COLUMN `longitude` VARCHAR(255) NULL;
ALTER TABLE `#__easyblog_post` ADD COLUMN `address` TEXT NULL;
ALTER TABLE `#__easyblog_category` ADD INDEX `easyblog_cat_private` (private);

ALTER TABLE `#__easyblog_drafts` ADD `address` TEXT NULL ,
ADD `latitude` VARCHAR( 255 ) NULL ,
ADD `longitude` VARCHAR( 255 ) NULL;

CREATE TABLE IF NOT EXISTS `#__easyblog_team_groups` (
  `team_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  KEY `team_id` (`team_id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `#__easyblog_team_groups` ADD INDEX `easyblog_team_group` (group_id);
ALTER TABLE `#__easyblog_post` ADD `system` tinyint unsigned NULL DEFAULT 0;
ALTER TABLE `#__easyblog_post` ADD `source` VARCHAR(255) NOT NULL;

CREATE TABLE IF NOT EXISTS `#__easyblog_twitter_microblog` (
  `id_str` text NOT NULL,
  `oauth_id` int(11) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `created` datetime NOT NULL,
  `tweet_author` text NOT NULL,
  KEY `post_id` (`post_id`),
  FULLTEXT KEY `id_str` (`id_str`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__easyblog_feeds` (
  `id` bigint(20) NOT NULL auto_increment,
  `title` text NOT NULL,
  `url` text NOT NULL,
  `interval` int(11) NOT NULL,
  `cron` tinyint(3) NOT NULL,
  `item_creator` int(11) NOT NULL,
  `item_category` bigint(20) NOT NULL,
  `item_frontpage` tinyint(3) NOT NULL,
  `item_published` tinyint(3) NOT NULL,
  `item_content` text NOT NULL,
  `author` tinyint(3) NOT NULL,
  `params` text NOT NULL,
  `published` tinyint(3) NOT NULL,
  `created` datetime NOT NULL,
  `last_import` datetime NOT NULL,
  `flag` tinyint(3) default '0',
  PRIMARY KEY  (`id`),
  KEY `cron` (`cron`),
  KEY `item_creator` (`item_creator`),
  KEY `author` (`author`),
  KEY `item_frontpage` (`item_frontpage`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `#__easyblog_feeds_history` (
  `id` bigint(20) NOT NULL auto_increment,
  `feed_id` bigint(20) NOT NULL,
  `post_id` int(11) NOT NULL,
  `uid` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `feed_post_id` (`feed_id`,`post_id`),
  KEY `feed_uids` (`feed_id`, `uid` (255) )
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

ALTER TABLE `#__easyblog_category` ADD `level` int(11) unsigned DEFAULT 0;
ALTER TABLE `#__easyblog_category` ADD `lft` int(11) unsigned DEFAULT 0;
ALTER TABLE `#__easyblog_category` ADD `rgt` int(11) unsigned DEFAULT 0;
ALTER TABLE `#__easyblog_category` ADD `default` tinyint(1) unsigned DEFAULT 0;
ALTER TABLE `#__easyblog_category` ADD INDEX `easyblog_cat_lft` (`lft`);
ALTER TABLE `#__easyblog_post` ADD `robots` TEXT NOT NULL;
ALTER TABLE `#__easyblog_drafts` ADD `robots` TEXT NOT NULL;