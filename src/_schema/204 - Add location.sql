
CREATE TABLE `location`
(
	`location_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,

	`name` VARCHAR(255) NOT NULL,
	`name_slug` VARCHAR(255) NOT NULL DEFAULT '',
	`address` VARCHAR(255) NOT NULL,
	`website` VARCHAR(255),

	`latitude` DECIMAL(8,6) NOT NULL,
	`longitude` DECIMAL(9,6) NOT NULL,

	# `geo` POINT NOT NULL COMMENT 'For spatial index; set via triggers',

	PRIMARY KEY (`location_id`),
	# SPATIAL INDEX `geo` (`geo`),
	UNIQUE INDEX `person.unique.latlong` (`latitude`, `longitude`)
)
COMMENT='A location'
ENGINE=InnoDB;

# Add location_id to content
ALTER TABLE `content`
	ADD COLUMN `location_id` INT UNSIGNED NULL,
	ADD CONSTRAINT `FK.content.location` 
		FOREIGN KEY (`location_id`)
		REFERENCES `location` (`location_id`);

