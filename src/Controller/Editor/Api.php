<?php

namespace Controller\Editor;
use Model;
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
	 * Series
	 */
	public function get_series(): Series
	{
		return Model::series()->get($_GET['id'] ?? null);
	}

	public function put_series(array $data): Series
	{
		return Model::series()->save($data);
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
