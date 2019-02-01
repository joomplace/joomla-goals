
CREATE TABLE IF NOT EXISTS `#__goals_custom_plan_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `type` varchar(3) NOT NULL,
  `values` text NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

ALTER TABLE `#__goals_milistones` ADD `value` INT( 11 ) NOT NULL AFTER `description`;
ALTER TABLE `#__goals_milistonestemplates` ADD `value` INT( 11 ) NOT NULL AFTER `description`;