<?php

namespace Controller\Manage\Content;
use Model;
use \Data\File;

/**
 * Methods for content adding.
 */
class Api extends \Controller\Api
{
	protected $required_roles = ['editor'];


	public function get_fresh()
	{
		return Model::files()->fresh();
	}

	public function post_file(string $path): File
	{
		return Model::files()->file($path);
	}

	public function put_content(array $data): File
	{
		var_dump($data);exit;
		return true;
	}

	public function put_series($data)
	{
		var_dump($data);exit;
		return true;
	}
}
