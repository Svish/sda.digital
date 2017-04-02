<?php

namespace Controller\Manage\Fresh;
use Model;
use \Data\File;

/**
 * API for handling fresh content.
 */
class Api extends \Controller\Api
{
	protected $required_roles = ['editor'];
	

	public function get_fresh_dirs()
	{
		return Model::fresh()
			->directories();
	}

	public function get_lookup_series()
	{
		return Model::series()
			->search($_GET['term'] ?? null);
	}

	public function get_fresh_content()
	{
		$content = Model::fresh()->content($_GET['path'] ?? null);
		$content = iterator_to_array($content, false);
		$path = $_GET['path'] ?? null;

		$locations = Model::locations()->all_select();		
		$roles = Model::persons()->all_roles();

		return get_defined_vars();
	}

	public function get_tag_info()
	{
		$info = Model::fresh()
			->tag_info($_GET['path'] ?? null);
		return iterator_to_array($info);
	}

	public function put_content(array $data)
	{
		Model::fresh()->save($data);
	}
}
