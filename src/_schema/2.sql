
CREATE TABLE IF NOT EXISTS `user`
(
	`email` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`password_hash` varchar(255),
	`token_hash` varchar(255),

	PRIMARY KEY (`email`)
)
ENGINE=InnoDB;
