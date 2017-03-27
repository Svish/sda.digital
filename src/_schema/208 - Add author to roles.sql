
ALTER TABLE `content_person`
	CHANGE COLUMN `role` `role` ENUM('speaker','translator','author') NOT NULL DEFAULT 'speaker';
