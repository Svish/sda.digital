<?php

namespace Model;

use Data\Location;
use DB, Model;


class Locations extends \Model
{
	/**
	 * Get location by id or email.
	 */
	public function get($id = null): Location
	{
		return Location::get($id);
	}


	/**
	 * Delete location by id.
	 */
	public function delete(int $id): bool
	{
		return Location::delete($id);
	}


	/**
	 * Save location data.
	 */
	public function save(array $data): Location
	{
		$data = array_whitelist($data, [
			'location_id',
			'name',
			'address',
			'website',
			'longitude',
			'latitude',
			]);

		$x = $this->get($data['location_id'] ?? null);
		$x->set($data);
		$x->validate();
		$x->save();
		return $x;
	}


	/**
	 * Get all locations for <select>.
	 */
	public function all_select(): array
	{
		return DB::query('SELECT location_id, name FROM location ORDER BY name')
			->fetchArray();
	}


	/**
	 * Get all locations.
	 */
	public function all(): array
	{
		return DB::query('SELECT * FROM location ORDER BY name')
			->fetchAll(Location::class);
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
					location_id,
					name,
					name_slug,
					COUNT(DISTINCT content_id) 'count'
				FROM location
				$join JOIN content USING (location_id)
				GROUP BY location_id
				ORDER BY name")
			->fetchAll(Location::class);
	}




	/**
	 * For location/$id.
	 */
	public function for_page($id): Location
	{
		$x = self::get($id);

		$x->content_list = Model::content()->for_location($id);
		$x->series_list = Model::series()->for_location($id);
		$x->person_list = Model::persons()->for_location($id);

		return $x;
	}

	/**
	 * Locations of a series.
	 */
	public function for_series($sid): array
	{
		return DB::prepare("SELECT
					location.*,
					COUNT(DISTINCT content_id) 'count'
				FROM location
				INNER JOIN content USING (location_id)
				INNER JOIN series_content USING (content_id)
				WHERE series_id = ?
				GROUP BY location_id
				ORDER BY location.name")
			->execute([$sid])
			->fetchAll(Location::class);
	}
	
}
