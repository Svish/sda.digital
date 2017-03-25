
CREATE TABLE `fresh_log`
(
	`user_id` INT UNSIGNED NOT NULL,
	`content_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`user_id`, `content_id`),

	CONSTRAINT `fk.fresh_log.content`
		FOREIGN KEY (`content_id`)
		REFERENCES `content` (`content_id`)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT,

	CONSTRAINT `fk.fresh_log.user`
		FOREIGN KEY (`user_id`)
		REFERENCES `user` (`user_id`)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT
)
COMMENT='Log of freshly added files to possibly make series out of'
ENGINE=InnoDB;
