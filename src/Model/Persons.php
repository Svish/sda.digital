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
	 * Get all roles for persons in content.
	 */
	public function all_roles(): array
	{
		// TODO: Get from DB
		return [
			['role' => 'speaker', 'label' => 'Taler'],
			['role' => 'translator', 'label' => 'Oversetter'],
			['role' => 'author', 'label' => 'Forfatter'],
		];
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
	public function for_page(int $id): Person
	{
		$x = self::get($id);
		$x->content_list = Model::content()->for_person($x);
		$x->series_list = Model::series()->for_person($x);

		return $x;
	}



	/**
	 * Speakers of a series.
	 */
	public function for_series(\Data\Series $p): array
	{
		return DB::prepare("SELECT
					person.*,
					role,
					COUNT(DISTINCT content_id) 'count',
					(SELECT COUNT(*) FROM series_content WHERE series_id = series.series_id) 'total'
				FROM person
				INNER JOIN content_person USING (person_id)
				INNER JOIN series_content USING (content_id)
				INNER JOIN series USING (series_id)
				WHERE series_id = ?
				GROUP BY person_id
				ORDER BY role, name")
			->execute([$p->id()])
			->fetchAll(Person::class);
	}



	/**
	 * Participants of content.
	 */
	public function for_content(\Data\Content $c): array
	{
		return DB::prepare("SELECT
					person.*,
					content_person.role			
				FROM person
				INNER JOIN content_person USING (person_id)
				WHERE content_id = ?
				ORDER BY role, name")
			->execute([$c->id()])
			->fetchAll(Person::class);
	}



	/**
	 * Participants of content; just what's needed for content editor.
	 */
	public function for_content_editor(\Data\Content $c): array
	{
		return DB::prepare("SELECT
					person.person_id,
					person.name,
					content_person.role			
				FROM person
				INNER JOIN content_person USING (person_id)
				WHERE content_id = ?
				ORDER BY role, name")
			->execute([$c->id()])
			->fetchArray();
	}



	/**
	 * Speakers at a location.
	 */
	public function for_location(\Data\Location $l): array
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
			->execute([$l->id()])
			->fetchAll(Person::class);
	}
}
