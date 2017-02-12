<?php

namespace Controller\Manage\Content;
use View, Model;

/**
 * Methods for content adding.
 */
class Api extends \Controller\Api
{
	protected $required_roles = ['editor'];


	public function get_fresh()
	{
		return Model::freshness()->get_fresh();
	}


	public function get_file_info($data)
	{
		var_dump($data);exit;
	}

	public function put_content($data)
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
