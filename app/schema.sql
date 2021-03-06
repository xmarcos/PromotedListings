CREATE TABLE `facebook_access_token` (
  `meli_user_id` bigint(20) unsigned NOT NULL,
  `access_token` varchar(2048) NOT NULL DEFAULT '',
  `expires` int(11) unsigned DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`meli_user_id`),
  KEY `expires` (`expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `facebook_ad_account` (
  `account_id` bigint(20) unsigned NOT NULL,
  `meli_user_id` bigint(20) unsigned NOT NULL,
  `id` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `enabled` int(1) unsigned NOT NULL DEFAULT '0',
  `data` text,
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`account_id`),
  KEY `meli_user_id` (`meli_user_id`),
  KEY `status` (`status`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `meli_account` (
  `user_id` bigint(20) unsigned NOT NULL,
  `nickname` varchar(255) NOT NULL DEFAULT '',
  `country_id` char(2) DEFAULT NULL,
  `site_id` char(3) DEFAULT NULL,
  `data` text,
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  KEY `country_id` (`country_id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
