<?php

namespace Model;
use Data\Content as C;
use DB, Model;

/**
 * Helper model for front page.
 */
class Front extends Model
{
	public function latest_recorded(): array
	{
		return DB::query("SELECT
					content.*,
					GROUP_CONCAT(DISTINCT person.name
						ORDER BY content_person.role, person.name
						SEPARATOR ', ') 'persons',
					GROUP_CONCAT(DISTINCT file.type ORDER BY file.type) 'types'
				FROM content
				INNER JOIN file USING (content_id)
				LEFT OUTER JOIN content_person USING (content_id)
				LEFT OUTER JOIN person USING (person_id)
				WHERE time IS NOT NULL
				GROUP BY content_id
				ORDER BY time DESC
				LIMIT 7")
			->execute()
			->fetchAll(C::class);
	}
}
