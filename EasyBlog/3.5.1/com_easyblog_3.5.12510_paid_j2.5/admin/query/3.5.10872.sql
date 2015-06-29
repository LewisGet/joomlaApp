ALTER TABLE `#__easyblog_tag` ADD INDEX `easyblog_tag_title` ( `title` );
ALTER TABLE `#__easyblog_tag` ADD INDEX `easyblog_tag_published` ( `published` );
ALTER TABLE `#__easyblog_tag` ADD INDEX `easyblog_tag_alias` ( `alias` );
ALTER TABLE `#__easyblog_tag` ADD INDEX `easyblog_tag_query1` (`published`, `id`, `title`);