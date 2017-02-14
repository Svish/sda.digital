
DROP TABLE IF EXISTS `speaker`;
CREATE TABLE `speaker`
(
	`speaker_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`name_slug` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`speaker_id`),
	UNIQUE INDEX `speaker.unique.name` (`name`)
)
COMMENT='A single speaker'
ENGINE=InnoDB;

DROP TABLE IF EXISTS `content_speaker`;
CREATE TABLE `content_speaker`
(
	`content_id` INT UNSIGNED NOT NULL,
	`speaker_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`content_id`, `speaker_id`),
	CONSTRAINT `fk.content_speaker.content`
		FOREIGN KEY (`content_id`)
		REFERENCES `content` (`content_id`)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT,
	CONSTRAINT `fk.content_speaker.speaker`
		FOREIGN KEY (`speaker_id`)
		REFERENCES `speaker` (`speaker_id`)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT
)
COMMENT='The speaker(s) of content'
ENGINE=InnoDB;
