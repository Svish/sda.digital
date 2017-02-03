
DROP TABLE IF EXISTS `series`;
CREATE TABLE `series`
(
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL,
	`title_slug` VARCHAR(255) NOT NULL,
	`added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COMMENT='An ordered series of content (series of meetings, etc)'
ENGINE=InnoDB;

DROP TABLE IF EXISTS `series_content`;
CREATE TABLE `series_content`
(
	`series_id` INT UNSIGNED NOT NULL,
	`content_id` INT UNSIGNED NOT NULL,
	`n` SMALLINT UNSIGNED COMMENT 'Use content time if null',
	PRIMARY KEY (`series_id`, `content_id`)
)
COMMENT='The content in a series, with optional ordering'
ENGINE=InnoDB;
