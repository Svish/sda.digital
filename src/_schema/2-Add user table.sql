
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`
(
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`password_hash` varchar(255),
	`token_hash` varchar(255),

	PRIMARY KEY (`id`),
	UNIQUE INDEX `user.unique.email` (`email`)
)
COMMENT='User accounts'
ENGINE=InnoDB;
