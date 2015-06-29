ALTER TABLE `#__easyblog_post` ADD `language` CHAR( 7 ) NOT NULL;
ALTER TABLE `#__easyblog_drafts` ADD `language` CHAR( 7 ) NOT NULL;
ALTER TABLE `#__easyblog_meta` ADD `indexing` INT( 3 ) NOT NULL DEFAULT 1;
ALTER TABLE `#__easyblog_drafts` ADD `autopost_centralized` TEXT NOT NULL AFTER `autopost`;
ALTER TABLE `#__easyblog_post` ADD `image` TEXT NULL;

CREATE TABLE IF NOT EXISTS `#__easyblog_external` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `source` text NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `external_groups_post_id` (`post_id`),
  KEY `external_groups_group_id` (`uid`),
  KEY `external_groups_posts` (`uid`,`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;