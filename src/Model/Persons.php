<?php

namespace Model;

use Data\Person;
use DB, Model;


class Persons extends Model
{
	/**
	 * Get person by id.
	 */
	public function get($id = null): Person
	{
		return Person::get($id);
	}


	/**
	 * Save person data.
	 */
	public function save(array $data): Person
	{
		$data = array_whitelist($data, [
			'person_id',
			'name',
			]);

		$x = $this->get($data['person_id'] ?? null);
		$x->set($data);
		$x->validate();
		$x->save();
		return $x;
	}


	/**
	 * Find person by name; return new person if none found.
	 */
	public function find(string $name): Person
	{
		$x = DB::prepare('SELECT * 
								FROM person 
								WHERE name=:name')
			->execute(['name' => $name])
			->fetchFirst(Person::class);

		if($x)
			return $x;

		$x = new Person;
		$x->name = $name;
		return $x;
	}


	/**
	 * Delete person by id.
	 */
	public function delete(int $id): bool
	{
		return Person::delete($id);
	}


	/**
	 * Get all persons.
	 */
	public function all(): array
	{
		return DB::query('SELECT * FROM person ORDER BY name')
			->fetchAll(Person::class);
	}



	/**
	 * For person/index.
	 */
	public function for_index(): array
	{
		$user = Model::users()->logged_in();
		$join = $user && $user->has_roles(['editor'])
			? 'LEFT OUTER'
			: 'INNER';
		return DB::query("SELECT
					person.*,
					COUNT(DISTINCT content_id) 'count'
				FROM person
				$join JOIN content_person USING (person_id)
				GROUP BY person_id
				ORDER BY name")
			->fetchAll(Person::class);
	}



	/**
	 * For person/$id.
	 */
	public function for_page($id): Person
	{
		$x = self::get($id);
		$x->content_list = Model::content()->for_person($id);
		$x->series_list = Model::series()->for_person($id);

		return $x;
	}



	/**
	 * Speakers of a series.
	 */
	public function for_series($sid): array
	{
		return DB::prepare("SELECT
					person.*,
					role,
					COUNT(DISTINCT content_id) 'count'
				FROM person
				INNER JOIN content_person USING (person_id)
				INNER JOIN series_content USING (content_id)
				WHERE series_id = ?
				GROUP BY person_id
				ORDER BY role, name")
			->execute([$sid])
			->fetchAll(Person::class);
	}



	/**
	 * Participants of content.
	 */
	public function for_content($cid): array
	{
		return DB::prepare("SELECT
					person.*,
					content_person.role			
				FROM person
				INNER JOIN content_person USING (person_id)
				WHERE content_id = ?
				ORDER BY role, name")
			->execute([$cid])
			->fetchAll(Person::class);
	}



	/**
	 * Speakers at a location.
	 */
	public function for_location($lid): array
	{
		return DB::prepare("SELECT
					person.*,
					COUNT(DISTINCT content_id) 'count'
				FROM person
				INNER JOIN content_person USING (person_id)
				INNER JOIN content USING (content_id)
				WHERE location_id = ?
					AND role = 'speaker'
				GROUP BY person_id
				ORDER BY name")
			->execute([$lid])
			->fetchAll(Person::class);
	}
}
