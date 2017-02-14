
DROP TABLE IF EXISTS `content`;
CREATE TABLE `content`
(
	`content_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL,
	`title_slug` VARCHAR(255) NOT NULL,
	`summary` TEXT,
	`time` DATETIME COMMENT 'When it happened, if known',
	`added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`content_id`)
)
COMMENT='Group of versions of the same file (audio, video, presentations, etc...)'
ENGINE=InnoDB;

DROP TABLE IF EXISTS `file`;
CREATE TABLE `file`
(
	`file_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`content_id` INT UNSIGNED NOT NULL,
	`path` VARCHAR(255) NOT NULL COMMENT 'Location on disk',
	`filename` VARCHAR(255) NOT NULL COMMENT 'File name',
	`extension` VARCHAR(4) NOT NULL COMMENT 'File extension',
	`hash` VARCHAR(255) NOT NULL COMMENT 'File hash',
	`type` VARCHAR(255) NOT NULL COMMENT 'Mime type',
	`encoding` VARCHAR(255) NOT NULL COMMENT 'Encoding, via finfo FILEINFO_MIME_ENCODING',
	`description` VARCHAR(255) NOT NULL COMMENT 'Description, via finfo FILEINFO_NONE',
	PRIMARY KEY (`file_id`),
	UNIQUE INDEX `unique.file.path` (`path`),
	UNIQUE INDEX `unique.file.hash` (`hash`),
	CONSTRAINT `fk.file.content`
		FOREIGN KEY (`content_id`)
		REFERENCES `content` (`content_id`)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT
)
COMMENT='A content file on disk'
ENGINE=InnoDB;
