
ALTER TABLE `content_person`
	DROP PRIMARY KEY,
	ADD PRIMARY KEY (`content_id`, `person_id`, `role`);
