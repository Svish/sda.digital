
DROP TABLE IF EXISTS `file`;
CREATE TABLE `file`
(
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`content_id` INT UNSIGNED NOT NULL,
	`path` VARCHAR(255) NOT NULL COMMENT 'Location on disk',
	`filename` VARCHAR(255) NOT NULL COMMENT 'File name',
	`extension` VARCHAR(4) NOT NULL COMMENT 'File extension',
	`hash` VARCHAR(255) NOT NULL COMMENT 'File hash',
	`type` VARCHAR(4) NOT NULL COMMENT 'Mime type',
	`encoding` VARCHAR(4) NOT NULL COMMENT 'Encoding, via finfo FILEINFO_MIME_ENCODING',
	`description` VARCHAR(4) NOT NULL COMMENT 'Description, via finfo FILEINFO_NONE',
	PRIMARY KEY (`id`),
	CONSTRAINT `fk_file_content` 
		FOREIGN KEY (`content_id`) 
		REFERENCES `content` (`id`) 
		ON UPDATE CASCADE 
		ON DELETE CASCADE
)
COMMENT='A content file on disk'
ENGINE=InnoDB;

DROP TABLE IF EXISTS `content`;
CREATE TABLE `content`
(
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL,
	`title_slug` VARCHAR(255) NOT NULL,
	`summary` TEXT,
	`time` DATETIME COMMENT 'When it happened, if known',
	`added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COMMENT='Group of versions of the same file (audio, video, presentations, etc...)'
ENGINE=InnoDB;

DROP TABLE IF EXISTS `content_speaker`;
CREATE TABLE `content_speaker`
(
	`content_id` INT UNSIGNED NOT NULL,
	`speaker_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`content_id`, `speaker_id`),
	CONSTRAINT `fk.content_speaker.content` 
		FOREIGN KEY (`content_id`) 
		REFERENCES `content` (`id`) 
		ON UPDATE CASCADE 
		ON DELETE CASCADE,
	CONSTRAINT `fk.content_speaker.speaker` 
		FOREIGN KEY (`speaker_id`) 
		REFERENCES `speaker` (`id`) 
		ON UPDATE CASCADE 
		ON DELETE CASCADE
)
COMMENT='The speaker(s) of content'
ENGINE=InnoDB;

