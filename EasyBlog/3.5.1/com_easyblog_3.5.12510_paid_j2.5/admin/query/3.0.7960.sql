CREATE TABLE IF NOT EXISTS `#__easyblog_acl_filters` (
  `content_id` bigint(20) unsigned NOT NULL,
  `disallow_tags` text NOT NULL,
  `disallow_attributes` text NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;