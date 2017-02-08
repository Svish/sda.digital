
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


# TODO: Negative n for series files?

DROP TABLE IF EXISTS `series_content`;
CREATE TABLE `series_content`
(
	`series_id` INT UNSIGNED NOT NULL,
	`content_id` INT UNSIGNED NOT NULL,
	`n` SMALLINT COMMENT 'Use content time if null',
	PRIMARY KEY (`series_id`, `content_id`),
	CONSTRAINT `fk.series_content.content` 
		FOREIGN KEY (`content_id`) 
		REFERENCES `content` (`id`) 
		ON UPDATE CASCADE 
		ON DELETE CASCADE,
	CONSTRAINT `fk.series_content.series` 
		FOREIGN KEY (`series_id`) 
		REFERENCES `series` (`id`) 
		ON UPDATE CASCADE 
		ON DELETE CASCADE
)
COMMENT='The content in a series, with optional ordering'
ENGINE=InnoDB;
