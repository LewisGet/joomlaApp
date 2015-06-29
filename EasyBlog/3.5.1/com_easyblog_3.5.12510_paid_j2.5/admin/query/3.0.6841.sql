CREATE TABLE IF NOT EXISTS `#__easyblog_xml_wpdata` (
  `id` bigint(20) NOT NULL auto_increment,
  `session_id` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `source` varchar(15) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `xml_wpdate_session` (`session_id`),
  KEY `xml_wpdate_post_source` (`post_id`, `source`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;