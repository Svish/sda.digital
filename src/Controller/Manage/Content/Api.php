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


	public function get_fresh_content()
	{
		return Model::fresh()->content($_GET['path'] ?? null);
	}
	
	public function get_fresh_dirs()
	{
		return Model::fresh()->directories();
	}

	public function get_analyze_file()
	{
		return Model::fresh()->analyze($_GET['path'] ?? null);
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
