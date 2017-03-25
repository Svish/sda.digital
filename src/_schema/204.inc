<?php

/*

// Set geo on insert
$pdo->exec('
	CREATE TRIGGER `location_before_insert`
		BEFORE INSERT ON `location` FOR EACH ROW
	BEGIN
		SET NEW.geo = POINT(NEW.longitude, NEW.latitude);
	END
');

// Update geo on update
$pdo->exec('
	CREATE TRIGGER `location_before_update` 
		BEFORE UPDATE ON `location` FOR EACH ROW
	BEGIN
		SET NEW.geo = POINT(NEW.longitude, NEW.latitude);
	END
');
*/
