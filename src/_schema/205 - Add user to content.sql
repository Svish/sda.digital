
# Add user to content
ALTER TABLE `content`
	ADD COLUMN `user_id` INT UNSIGNED NOT NULL,
	ADD CONSTRAINT `FK.content.user` 
		FOREIGN KEY (`user_id`)
		REFERENCES `user` (`user_id`);

UPDATE `content` SET `user_id` = 1;
