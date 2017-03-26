<?php

namespace Model;
use Data\Content as C;
use DB, Model;


class Content extends Model
{
	const DIR = '_'.DIRECTORY_SEPARATOR;

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
	public function for_page($id): C
	{
		$x = self::get($id);
		$x->load_relations('location', 'file_list');

		$x->person_list = Model::persons()->for_content($id);
		$x->series_list = Model::series()->for_content($id);

		return $x;
	}


	/**
	 * Content in a series.
	 */
	public function for_series($sid): array
	{
		return DB::prepare("SELECT
					n,
					content.*,
					person.name 'speaker'

				FROM content
				INNER JOIN series_content USING (content_id)
				INNER JOIN content_person USING (content_id)
				INNER JOIN person USING (person_id)
				WHERE series_id = ?
					AND content_person.role = 'speaker'
				ORDER BY n")
			->execute([$sid])
			->fetchAll(C::class);

		return $x;
	}


	/**
	 * Content of a speaker.
	 */
	public function for_person($pid): array
	{
		return DB::prepare("SELECT
					content.*,
					role,
					location.name 'location'
				FROM content
				INNER JOIN content_person USING (content_id)
				INNER JOIN person USING (person_id)
				LEFT OUTER JOIN location USING (location_id)
				WHERE person_id = ?
				GROUP BY content_id
				ORDER BY content.title")
			->execute([$pid])
			->fetchAll(C::class);

		return $x;
	}


	/**
	 * Content at a location.
	 */
	public function for_location($lid): array
	{
		return DB::prepare("SELECT
					content.*
				FROM content
				WHERE location_id = ?
				ORDER BY content.created DESC")
			->execute([$lid])
			->fetchAll(C::class);

		return $x;
	}
}
