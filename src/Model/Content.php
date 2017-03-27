<?php

namespace Model;
use Data\Content as C;
use DB, Model;


class Content extends Model
{
	/**
	 * Get location by id or email.
	 */
	public function get($id = null): C
	{
		return C::get($id);
	}

	/**
	 * For location/$id.
	 */
	public function for_page(int $id): C
	{
		$x = self::get($id);
		$x->load_relations('location', 'file_list');

		$x->person_list = Model::persons()->for_content($x);
		$x->series_list = Model::series()->for_content($x);

		return $x;
	}


	/**
	 * Content in a series.
	 */
	public function for_series(\Data\Series $s): array
	{
		return DB::prepare("SELECT
					n,
					content.*,
					GROUP_CONCAT(DISTINCT person.name
						ORDER BY content_person.role, person.name
						SEPARATOR ', ') 'speakers'
				FROM content
				INNER JOIN series_content USING (content_id)
				LEFT OUTER JOIN content_person USING (content_id)
				LEFT OUTER JOIN person USING (person_id)
				WHERE series_id = ?
				GROUP BY content_id
				ORDER BY n")
			->execute([$s->id()])
			->fetchAll(C::class);
	}


	/**
	 * Content of a speaker.
	 */
	public function for_person(\Data\Person $p): array
	{
		return DB::prepare("SELECT
					content.*,
					GROUP_CONCAT(DISTINCT content_person.role
						ORDER BY content_person.role
						SEPARATOR ', ') 'role',
					location.name 'location'
				FROM content
				INNER JOIN content_person USING (content_id)
				INNER JOIN person USING (person_id)
				LEFT OUTER JOIN location USING (location_id)
				WHERE person_id = ?
				GROUP BY content_id
				ORDER BY content.title")
			->execute([$p->id()])
			->fetchAll(C::class);
	}


	/**
	 * Content at a location.
	 */
	public function for_location(\Data\Location $l): array
	{
		return DB::prepare("SELECT
					content.*,
					0 'location',
					GROUP_CONCAT(DISTINCT person.name
						ORDER BY content_person.role
						SEPARATOR ', ') 'speakers'
				FROM content
				INNER JOIN location USING (location_id)
				LEFT OUTER JOIN content_person USING (content_id)
				LEFT OUTER JOIN person USING (person_id)
				WHERE location_id = ?
				GROUP BY content_id
				ORDER BY content.created DESC")
			->execute([$l->id()])
			->fetchAll(C::class);
	}


	/**
	 * Content of a speaker.
	 */
	public function for_file(\Data\File $f): array
	{
		return DB::prepare("SELECT
					content.*,
					GROUP_CONCAT(DISTINCT person.name
						ORDER BY content_person.role, person.name
						SEPARATOR ', ') 'speakers',
					location.name 'location'
				FROM content
				INNER JOIN file USING (content_id)
				LEFT OUTER JOIN content_person USING (content_id)
				LEFT OUTER JOIN person USING (person_id)
				LEFT OUTER JOIN location USING (location_id)
				WHERE file_id = ?
				GROUP BY content_id
				ORDER BY content.title")
			->execute([$f->id()])
			->fetchAll(C::class);
	}
}
