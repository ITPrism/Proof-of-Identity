CREATE TABLE IF NOT EXISTS `#__identityproof_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `filename` varchar(32) DEFAULT NULL,
  `private` blob,
  `public` blob,
  `meta_data` varchar(255) DEFAULT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `note` varchar(255) DEFAULT NULL,
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__identityproof_users` (
  `id` int(10) unsigned NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;