SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `#__goals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(250) NOT NULL,
  `deadline` datetime NOT NULL,
  `metric` varchar(250) NOT NULL,
  `start` float NOT NULL,
  `finish` float NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `is_complete` tinyint(3) unsigned NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goalstemplates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(250) NOT NULL,
  `deadline` datetime NOT NULL,
  `metric` varchar(250) NOT NULL,
  `start` float NOT NULL,
  `finish` float NOT NULL,
  `period` float NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__goals_dashboard_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__goals_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL,
  `published` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_categories_xref` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_custom_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `type` varchar(3) NOT NULL,
  `values` text NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goals_custom_plan_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `type` varchar(3) NOT NULL,
  `values` text NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_custom_fields_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goals_custom_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_custom_groups_xref` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_habits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `type` varchar(1) NOT NULL,
  `date` date NOT NULL,
  `days` varchar(14) NOT NULL,
  `weight` int(11) NOT NULL,
  `finish` int(11) NOT NULL,
  `color` varchar(7) NOT NULL,
  `complete` tinyint(4) NOT NULL,
  `uid` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_habitstemplates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `type` varchar(1) NOT NULL,
  `days` varchar(14) NOT NULL,
  `weight` int(11) NOT NULL,
  `finish` int(11) NOT NULL,
  `color` varchar(7) NOT NULL,
  `complete` tinyint(4) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_habits_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hid` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `status` tinyint(4) NOT NULL,
  `result` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_milistones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `duedate` datetime NOT NULL,
  `gid` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL,
  `color` varchar(7) NOT NULL,
  `cdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_milistonestemplates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `daysto` int(11) NOT NULL,
  `gid` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL,
  `color` varchar(7) NOT NULL,
  `cdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_plans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(250) NOT NULL,
  `deadline` datetime NOT NULL,
  `metric` varchar(250) NOT NULL,
  `start` float NOT NULL,
  `finish` float NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `is_complete` tinyint(3) unsigned NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_planstemplates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(250) NOT NULL,
  `deadline` datetime NOT NULL,
  `metric` varchar(250) NOT NULL,
  `start` float NOT NULL,
  `finish` float NOT NULL,
  `period` float NOT NULL,
  `is_complete` tinyint(3) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goals_plans_categories_xref` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_plans_xref` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `values` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goals_plantasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `date` datetime NOT NULL,
  `c_date` datetime NOT NULL,
  `sid` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL,
  `value` float NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goals_plantaskstemplates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `period` int(11) NOT NULL,
  `sid` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL,
  `value` float NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__goals_plantasks_xref` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `values` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goals_plan_custom_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `type` varchar(3) NOT NULL,
  `values` text NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goals_stages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(4) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `duedate` datetime NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `color` varchar(7) NOT NULL,
  `cdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goals_stagestemplates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `daysto` int(11) NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL,
  `color` varchar(7) NOT NULL,
  `cdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goals_tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `date` datetime NOT NULL,
  `gid` int(10) unsigned NOT NULL,
  `value` float NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goals_taskstemplates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `date` datetime NOT NULL,
  `gid` int(10) unsigned NOT NULL,
  `value` float NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goals_tasks_xref` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `values` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__goals_xref` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `values` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;