<?php

namespace Model;
use Data\Content as C;
use DB, Model;

/**
 * Helper model for front page.
 */
class Front extends Model
{
	public function latest_added(): array
	{
		return DB::query("SELECT
					content.*,
					location.name 'location',
					GROUP_CONCAT(DISTINCT person.name
						ORDER BY content_person.role, person.name
						SEPARATOR ', ') 'speakers'
				FROM content
				LEFT OUTER JOIN content_person USING (content_id)
				LEFT OUTER JOIN person USING (person_id)
				LEFT OUTER JOIN location USING (location_id)
				GROUP BY content_id
				ORDER BY created DESC
				LIMIT 10")
			->execute()
			->fetchAll(C::class);
	}


	public function latest_recorded(): array
	{
		return DB::query("SELECT
					content.*,
					location.name 'location',
					GROUP_CONCAT(DISTINCT person.name
						ORDER BY content_person.role, person.name
						SEPARATOR ', ') 'speakers'
				FROM content
				LEFT OUTER JOIN content_person USING (content_id)
				LEFT OUTER JOIN person USING (person_id)
				LEFT OUTER JOIN location USING (location_id)
				WHERE time IS NOT NULL
				GROUP BY content_id
				ORDER BY time DESC
				LIMIT 10")
			->execute()
			->fetchAll(C::class);
	}
}
