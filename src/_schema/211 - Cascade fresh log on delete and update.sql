
ALTER TABLE `fresh_log`
	DROP FOREIGN KEY `fk.fresh_log.content`,
	DROP FOREIGN KEY `fk.fresh_log.user`;

ALTER TABLE `fresh_log`
	ADD CONSTRAINT `fk.fresh_log.content`
		FOREIGN KEY (`content_id`)
		REFERENCES `content` (`content_id`)
		ON UPDATE RESTRICT
		ON DELETE CASCADE,
	ADD CONSTRAINT `fk.fresh_log.user`
		FOREIGN KEY (`user_id`)
		REFERENCES `user` (`user_id`)
		ON UPDATE RESTRICT
		ON DELETE CASCADE;
