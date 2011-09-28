
/* ТАБЛИЦА ПОЛЬЗОВАТЕЛЕЙ */
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
	`id` 			INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	`login`			VARCHAR(100) NOT NULL,
	`password`		VARCHAR(100) NOT NULL,
	`surname`		VARCHAR(255),
	`name`			VARCHAR(255),
	`patronymic`	VARCHAR(255),
	`sex`			VARCHAR(10),
	`birthdate` 	VARCHAR(15),
	`country` 		VARCHAR(255),
	`city`		 	VARCHAR(255),
	`level`			SMALLINT,
	`active` 		CHAR(1),
	`regdate`		INT(10) UNSIGNED
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СТАТИЧЕСКИЕ СТРАНИЦЫ */
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` 				INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` 			TEXT NOT NULL,
  `alias` 			VARCHAR(255) NOT NULL,
  `body` 			TEXT,
  `author` 			INT(10) UNSIGNED NOT NULL,
  `published` 		BOOLEAN DEFAULT FALSE,
  `type`			CHAR(10),
  `meta_description` TEXT,
  `meta_keywords`	TEXT,
  `modif_date`		INT(10) UNSIGNED DEFAULT '0',
  `create_date`		INT(10) UNSIGNED DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX(`alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СОХРАНЕНИЕ ОШИБОК */
DROP TABLE IF EXISTS `error_log`;
CREATE TABLE `error_log` (
  `id` int(10) 	UNSIGNED NOT NULL AUTO_INCREMENT,
  `url`			TEXT,
  `description` TEXT,
  `session_dump` TEXT,
  `hash`		CHAR(32),
  `lastdate` 	INT(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ПОЛНАЯ ПОЛЬЗОВАТЕЛЬСКАЯ СТАТИСТИКА */
DROP TABLE IF EXISTS `user_statistics`;
CREATE TABLE `user_statistics` (
  `id` 				INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` 			INT(10) UNSIGNED DEFAULT 0,
  `request_urls`	TEXT,
  `user_ip`			VARCHAR(255),
  `referer`			VARCHAR(255),
  `user_agent_raw`	VARCHAR(255),
  `has_js`			BOOLEAN,
  `browser_name`	VARCHAR(50),
  `browser_version`	VARCHAR(50),
  `screen_width`	SMALLINT UNSIGNED,
  `screen_height`	SMALLINT UNSIGNED,
  `date`			INT(10) UNSIGNED,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


/* ТЕСТОВЫЕ РАЗДЕЛЫ */
DROP TABLE IF EXISTS `test_sections`;
CREATE TABLE `test_sections` (
  `id`			int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name`		VARCHAR(255),
  `alias`		VARCHAR(255),
  `published`	CHAR(1),
  `date` 		INT(10) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ТЕСТОВЫЕ КАТЕГОРИИ */
DROP TABLE IF EXISTS `test_categories`;
CREATE TABLE `test_categories` (
  `id`			int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `section_id`	INT,
  `name`		VARCHAR(255),
  `alias`		VARCHAR(255),
  `published`	CHAR(1),
  `date` 		INT(10) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ТЕСТОВЫЕ ОБЪЕКТЫ */
DROP TABLE IF EXISTS `test_items`;
CREATE TABLE `test_items` (
  `id`			int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category_id`	INT,
  `item_name`	VARCHAR(255),
  `item_text`	TEXT,
  `published`	CHAR(1),
  `date` 		INT(10) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
