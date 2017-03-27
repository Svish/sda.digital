<?php

namespace Controller\Editor;
use Model, View;
use Data\Person;
use Data\Series;
use Data\Location;

/**
 * Editor API.
 */
class Api extends \Controller\Api
{
	protected $required_roles = ['editor'];


	/**
	 * Content
	 */
	public function get_content(): Content
	{
		return Model::content()->get($_GET['id'] ?? null);
	}

	public function put_content(array $data): Content
	{
		return Model::content()->save($data);
	}

	public function delete_content(int $id)
	{
		Model::content()->delete($id);
	}


	/**
	 * Person
	 */
	public function get_person(): Person
	{
		return Model::persons()->get($_GET['id'] ?? null);
	}

	public function put_person(array $data): Person
	{
		return Model::persons()->save($data);
	}

	public function delete_person(int $id)
	{
		Model::persons()->delete($id);
	}


	/**
	 * Series, with fresh content to potentially add
	 */
	public function get_series()
	{
		$series = Model::series()->get($_GET['id'] ?? null);
		
		$content = Model::fresh()->mine();
		$content = View::template([
				'content_list' => $content,
				'fresh' => true,
				], 'list/content')
			->render('text/html');

		return get_defined_vars();
	}

	public function put_series(array $data): Series
	{
		$x = Model::series()
			->save($data['series'] ?? $data);

		Model::series()
			->save_content($data['content'] ?? null, $x);
			
		return $x;
	}

	public function delete_series(int $id)
	{
		Model::series()->delete($id);
	}


	/**
	 * Location
	 */
	public function get_location(): Location
	{
		return Model::locations()->get($_GET['id'] ?? null);
	}

	public function put_location(array $data): Location
	{
		return Model::locations()->save($data);
	}

	public function delete_location(int $id)
	{
		Model::locations()->delete($id);
	}
}
