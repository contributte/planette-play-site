-- default user  architect : kreslo
-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` char(60) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `role` varchar(30) COLLATE utf8_czech_ci NOT NULL DEFAULT 'user',
  `active` char(1) COLLATE utf8_czech_ci NOT NULL DEFAULT '1',
  `name` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `avatar` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `change_email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `change_email_requested` datetime NOT NULL,
  `change_email_tokenOne` char(60) COLLATE utf8_czech_ci NOT NULL,
  `change_email_tokenTwo` char(60) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `active`, `name`, `avatar`, `change_email`, `change_email_requested`, `change_email_tokenOne`, `change_email_tokenTwo`) VALUES
(1, 'architect',  '$2y$10$K.JEAIhI/bmk2Kas2uxFi.3Y.qJ6LZNw44X5k9Lq81R27wgcZqsSu', 'info@aprila.cz', 'root', '1',  'Architect',  '', '', '0000-00-00 00:00:00',  '', '');

DROP TABLE IF EXISTS `users_password_reset`;
CREATE TABLE `users_password_reset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `salt` char(32) COLLATE utf8_czech_ci NOT NULL,
  `token` char(64) COLLATE utf8_czech_ci NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  CONSTRAINT `userId` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;



-- Knowledge base


DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `translation_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `slug` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  `language` enum('cs','en') COLLATE utf8_czech_ci NOT NULL,
  `views` int(11) NOT NULL,
  `document_state` enum('public','draft','deleted') COLLATE utf8_czech_ci NOT NULL DEFAULT 'public',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `article_image`;
CREATE TABLE `article_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `article_id` int(11) NOT NULL,
  `filename` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `note` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `article_image_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `article_like`;
CREATE TABLE `article_like` (
  `user_id` int(10) unsigned NOT NULL,
  `article_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  UNIQUE KEY `user_id_article_id` (`user_id`,`article_id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `article_like_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `article_like_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `article_tag`;
CREATE TABLE `article_tag` (
  `article_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  KEY `article_id` (`article_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `article_tag_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `article_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `type` enum('normal','type','category') COLLATE utf8_czech_ci NOT NULL DEFAULT 'normal',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

