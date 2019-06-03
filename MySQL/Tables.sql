CREATE TABLE IF NOT EXISTS `leonafx_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(16) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hwid` text NOT NULL,
  `hwid_status` int(1) NOT NULL DEFAULT 1,
  `email` text NOT NULL,
  `ip` varchar(50) NOT NULL,
  `rank` int(1) NOT NULL DEFAULT 0,
  `status` int(1) NOT NULL DEFAULT 0,
  `startdate` varchar(50) NOT NULL,
  `timeleft` int(11) NOT NULL DEFAULT 0,
  `secret_answer` text NOT NULL,
  `registered_date` varchar(50) NOT NULL,
  `registered_country` varchar(50) NOT NULL,
  `registered_useragent` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `leonafx_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `license` varchar(50) NOT NULL,
  `timeleft` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `lifetime` int(11) NOT NULL,
  `generate_on` varchar(50) NOT NULL,
  `generated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `leonafx_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` varchar(8) NOT NULL,
  `log_date` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `log_ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `log_username` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `log_hwid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `log_summary` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `log_status` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
