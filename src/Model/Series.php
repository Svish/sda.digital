<?php

namespace Model;

use Data\Series as Serie;
use DB, Model;


class Series extends Model
{
	/**
	 * Get person by id.
	 */
	public function get($id = null): Serie
	{
		return Serie::get($id);
	}


	/**
	 * Save series data.
	 */
	public function save($data): Serie
	{
		$data = array_whitelist($data, [
			'series_id',
			'title',
			]);
		$x = $this->get($data['series_id'] ?? null);
		$x->set($data);
		$x->validate();
		$x->save();
		return $x;		
	}


	/**
	 * Find person by name; return new person if none found.
	 */
	public function search(string $string): array
	{
		return DB::prepare('SELECT * 
								FROM series 
								WHERE title LIKE ?')
			->execute(["%$string%"])
			->fetchAll(Serie::class);
	}


	/**
	 * Delete person by id.
	 */
	public function delete(int $id): bool
	{
		return Serie::delete($id);
	}


	/**
	 * Get all persons.
	 */
	public function all(): array
	{
		return DB::query('SELECT * FROM series ORDER BY title')
			->fetchAll(Serie::class);
	}


	/**
	 * For location/index.
	 */
	public function for_index(): array
	{
		$user = Model::users()->logged_in();
		$join = $user && $user->has_roles(['editor'])
			? 'LEFT OUTER'
			: 'INNER';
		return DB::query("SELECT
					series.*,
					COUNT(DISTINCT content_id) 'count'
				FROM series
				$join JOIN series_content USING (series_id)
				GROUP BY series_id
				ORDER BY title")
			->fetchAll(Serie::class);
	}


	/**
	 * For series/$id.
	 */
	public function for_page($sid): Serie
	{
		$x = self::get($sid);

		$x->content_list = Model::content()->for_series($sid);
		$x->location_list = Model::locations()->for_series($sid);
		$x->person_list = Model::persons()->for_series($sid);
		
		return $x;
	}


	/**
	 * Series of a speaker.
	 */
	public function for_person(int $pid): array
	{
		return DB::query("SELECT
					series.*,
					COUNT(DISTINCT content_id) 'count'
				FROM series
				INNER JOIN series_content USING (series_id)
				INNER JOIN content USING (content_id)
				INNER JOIN content_person USING (content_id)
				WHERE person_id = $pid
					AND content_person.role = 'speaker'
				GROUP BY series_id
				ORDER BY title")
			->fetchAll(Serie::class);
	}

	/**
	 * Series at a location.
	 */
	public function for_location(int $lid): array
	{
		return DB::query("SELECT
					series.*,
					COUNT(DISTINCT content_id) 'count'
				FROM series
				INNER JOIN series_content USING (series_id)
				INNER JOIN content USING (content_id)
				WHERE location_id = $lid
				GROUP BY series_id
				ORDER BY title")
			->fetchAll(Serie::class);
	}

	/**
	 * Series with content.
	 */
	public function for_content(int $cid): array
	{
		return DB::query("SELECT
					series.*,
					series_content.n
				FROM series
				INNER JOIN series_content USING (series_id)
				WHERE content_id = $cid
				ORDER BY title")
			->fetchAll(Serie::class);
	}
}
