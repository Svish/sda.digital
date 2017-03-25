
CREATE TABLE `person`
(
	`person_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`name_slug` VARCHAR(255) NOT NULL DEFAULT '',	
	PRIMARY KEY (`person_id`),
	UNIQUE INDEX `person.unique.name` (`name`)
)
COMMENT='A person'
ENGINE=InnoDB;

CREATE TABLE `content_person`
(
	`content_id` INT UNSIGNED NOT NULL,
	`person_id` INT UNSIGNED NOT NULL,
	`role` ENUM('speaker', 'translator') NOT NULL DEFAULT 'speaker',
	PRIMARY KEY (`content_id`, `person_id`),
	CONSTRAINT `fk.content_person.content`
		FOREIGN KEY (`content_id`)
		REFERENCES `content` (`content_id`)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT,
	CONSTRAINT `fk.content_person.person`
		FOREIGN KEY (`person_id`)
		REFERENCES `person` (`person_id`)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT
)
COMMENT='The person(s) of content'
ENGINE=InnoDB;
