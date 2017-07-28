<?php

namespace Controller\Editor;
use Model, View;
use Data\Content;
use Data\Location;
use Data\Person;
use Data\Series;

/**
 * Editor API.
 */
class Api extends \Controller\Api
{
	protected $required_roles = ['editor'];


	/**
	 * Content
	 */
	private $content_fields = [
		'content_id',
		'title',
		'summary',
		'time',
		'location_id',
		'persons',
	];

	public function get_content(): array
	{
		$content = Model::content()->get($_GET['id'] ?? null);
		$content->persons = Model::persons()->for_content_editor($content);
		$content = array_whitelist($content->jsonData(), $this->content_fields);

		$locations = Model::locations()->all_select();
		$roles = Model::persons()->all_roles();

		return get_defined_vars();
	}

	public function put_content(array $data): Content
	{
		// HACK: valueAllowUnset removes property, rather than setting to null
		if( ! isset($data['location_id']))
			$data['location_id'] = null;

		$data = array_whitelist($data, $this->content_fields);
		$x = Model::content()->save($data);

		Model::content()
			->set_persons($data['persons'] ?? null, $x);

		return $x;
	}

	public function delete_content(int $id)
	{
		Model::content()->delete($id);
	}


	/**
	 * Person
	 */
	private $person_fields = [
		'person_id',
		'name',
	];

	public function get_person(): array
	{
		$x = Model::persons()->get($_GET['id'] ?? null);
		return array_whitelist($x->jsonData(), $this->person_fields);
	}

	public function put_person(array $data): Person
	{
		$data = array_whitelist($data, $this->person_fields);
		return Model::persons()->save($data);
	}

	public function delete_person(int $id)
	{
		Model::persons()->delete($id);
	}


	/**
	 * Series, with fresh content to potentially add
	 */
	private $series_fields = [
		'series_id',
		'title',
	];

	public function get_series(): array
	{
		$series = Model::series()->get($_GET['id'] ?? null);
		$series = array_whitelist($series->jsonData(), $this->series_fields);
		
		$content = Model::fresh()->mine();
		$content = View::layout([
				'content_list' => $content,
				'fresh' => true,
				], 'list/content')
			->render('text/html');

		return get_defined_vars();
	}

	public function put_series(array $data): Series
	{
		$x = Model::series()
			->save(array_whitelist($data['series'] ?? $data, $this->series_fields));

		Model::series()
			->set_content($data['content'] ?? null, $x);
			
		return $x;
	}

	public function delete_series(int $id)
	{
		Model::series()->delete($id);
	}


	/**
	 * Location
	 */
	private $location_fields = [
		'location_id',
		'name',
		'website',
		'address',
		'latitude',
		'longitude',
	];

	public function get_location(): array
	{
		$x = Model::locations()->get($_GET['id'] ?? null);
		return array_whitelist($x->jsonData(), $this->location_fields);
	}

	public function put_location(array $data): Location
	{
		$data = array_whitelist($data, [
			'location_id',
			'name',
			'website',
			'address',
			'latitude',
			'longitude',
			]);
		return Model::locations()->save($data);
	}

	public function delete_location(int $id)
	{
		Model::locations()->delete($id);
	}
}
