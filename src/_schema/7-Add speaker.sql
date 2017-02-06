
DROP TABLE IF EXISTS `speaker`;
CREATE TABLE `speaker`
(
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`name_slug` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `unique_name` (`name`)
)
COMMENT='A single speaker'
ENGINE=InnoDB;
