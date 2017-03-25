
CREATE TABLE `series`
(
	`series_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL,
	`title_slug` VARCHAR(255) NOT NULL DEFAULT '',
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`series_id`)
)
COMMENT='An ordered series of content (series of meetings, etc)'
ENGINE=InnoDB;

CREATE TABLE `series_content`
	(
	`series_id` INT UNSIGNED NOT NULL,
	`content_id` INT UNSIGNED NOT NULL,
	`added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`n` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (`series_id`, `content_id`),
	CONSTRAINT `fk.series_content.content.series`
		FOREIGN KEY (`content_id`)
		REFERENCES `content` (`content_id`)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT,
	CONSTRAINT `fk.series_content.series`
		FOREIGN KEY (`series_id`)
		REFERENCES `series` (`series_id`)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT
)
COMMENT='The content in a series, with optional ordering'
ENGINE=InnoDB;
