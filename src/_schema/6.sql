ALTER TABLE `user`
	ADD COLUMN `roles` SET('login','editor','admin') NULL DEFAULT 'login' AFTER `name`;
