ALTER DATABASE `sda_digital`
	DEFAULT CHARACTER SET = 'utf8'
	DEFAULT COLLATE 'utf8_danish_ci';

USE `sda_digital`;



CREATE TABLE IF NOT EXISTS `speaker`
(
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `series`
(
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `file`
(
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sha256` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB;
