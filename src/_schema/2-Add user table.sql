
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`
(
	`email` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`password_hash` varchar(255),
	`token_hash` varchar(255),

	PRIMARY KEY (`email`)
)
COMMENT='User accounts'
ENGINE=InnoDB;
