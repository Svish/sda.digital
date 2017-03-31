<?php

namespace Model;

use Data\Series as S;
use DB, Model;


class Series extends Model
{
	/**
	 * Get person by id.
	 */
	public function get($id = null): S
	{
		return S::get($id);
	}


	/**
	 * Save series content.
	 */
	public function save_content(array $data, S $series)
	{
		if($data === null)
			return;

		DB::begin();

		// Delete old series content
		DB::prepare('DELETE FROM series_content
				WHERE series_id = ?')
			->exec([$series->series_id]);
		
		// Insert new
		$uid = Model::users()->logged_in()->id();

		$in = DB::prepare('INSERT INTO series_content
				(series_id, content_id, n)
				VALUES (?, ?, ?)');
		$out = DB::prepare('DELETE FROM fresh_log
				WHERE content_id = ? AND user_id = ?');

		foreach($data as $n => $cid)
		{
			$in->exec([$series->series_id, $cid, $n + 1]);
			$out->exec([$cid, $uid]);
		}


		DB::commit();
	}

	/**
	 * Save series data.
	 */
	public function save($data): S
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
			->fetchAll(S::class);
	}


	/**
	 * Delete person by id.
	 */
	public function delete(int $id): bool
	{
		return S::delete($id);
	}


	/**
	 * Get all persons.
	 */
	public function all(): array
	{
		return DB::query('SELECT * FROM series ORDER BY title')
			->fetchAll(S::class);
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
					COUNT(DISTINCT content_id) 'count',
					GROUP_CONCAT(DISTINCT person.name
						ORDER BY 
							content_person.role,
							person.name
						SEPARATOR ', ') 'persons'
				FROM series
				$join JOIN series_content USING (series_id)
				LEFT OUTER JOIN content_person USING (content_id)
				LEFT OUTER JOIN person USING (person_id)
				GROUP BY series_id
				ORDER BY title")
			->fetchAll(S::class);
	}


	/**
	 * For series/$id.
	 */
	public function for_page(int $id): S
	{
		$x = self::get($id);

		$x->content_list = Model::content()->for_series($x);
		$x->location_list = Model::locations()->for_series($x);
		$x->person_list = Model::persons()->for_series($x);
		
		return $x;
	}


	/**
	 * Series of a speaker.
	 */
	public function for_person(\Data\Person $p): array
	{
		$pid = $p->id();
		return DB::query("SELECT
					series.*,
					COUNT(DISTINCT content_id) 'count',
					(SELECT COUNT(*) FROM series_content WHERE series_id = series.series_id) 'total',
					GROUP_CONCAT(DISTINCT content_person.role
						ORDER BY content_person.role
						SEPARATOR ', ') 'persons'
				FROM series
				INNER JOIN series_content USING (series_id)
				INNER JOIN content USING (content_id)
				INNER JOIN content_person USING (content_id)
				WHERE person_id = $pid
				GROUP BY series_id
				ORDER BY title")
			->fetchAll(S::class);
	}

	/**
	 * Series at a location.
	 */
	public function for_location(\Data\Location $l): array
	{
		$lid = $l->id();
		return DB::query("SELECT
					series.*,
					COUNT(DISTINCT content_id) 'count',
					(SELECT COUNT(*) FROM series_content WHERE series_id = series.series_id) 'total',
					GROUP_CONCAT(DISTINCT content_person.role
						ORDER BY content_person.role
						SEPARATOR ', ') 'persons'
				FROM series
				INNER JOIN series_content USING (series_id)
				INNER JOIN content USING (content_id)
				LEFT OUTER JOIN content_person USING (content_id)
				LEFT OUTER JOIN person USING (person_id)
				WHERE location_id = $lid
				GROUP BY series_id
				ORDER BY title")
			->fetchAll(S::class);
	}

	/**
	 * Series with content.
	 */
	public function for_content(\Data\Content $c): array
	{
		$cid = $c->id();
		return DB::query("SELECT
					series.*,
					series_content.n
				FROM series
				INNER JOIN series_content USING (series_id)
				WHERE content_id = $cid
				ORDER BY title")
			->fetchAll(S::class);
	}
}
