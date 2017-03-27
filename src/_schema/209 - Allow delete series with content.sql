
ALTER TABLE `series_content`
	DROP FOREIGN KEY `fk.series_content.content.series`,
	DROP FOREIGN KEY `fk.series_content.series`;

ALTER TABLE `series_content`
	ADD CONSTRAINT `fk.series_content.content.series` FOREIGN KEY (`content_id`) REFERENCES `content` (`content_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	ADD CONSTRAINT `fk.series_content.series` FOREIGN KEY (`series_id`) REFERENCES `series` (`series_id`) ON UPDATE CASCADE ON DELETE CASCADE;
