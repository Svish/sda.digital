
ALTER TABLE `file`
	ALTER `description` DROP DEFAULT;
ALTER TABLE `file`
	CHANGE COLUMN `description` `description` TEXT NOT NULL COMMENT 'Description, via finfo FILEINFO_NONE' COLLATE 'utf8_danish_ci' AFTER `encoding`;
