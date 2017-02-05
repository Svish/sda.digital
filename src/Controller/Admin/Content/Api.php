<?php

namespace Controller\Admin\Content;
use Session, View, Model;

/**
 * JSON API methods for content adding.
 */
class Api extends \Controller\Admin
{
	protected $required_roles = ['editor'];

	public function get($what = null)
	{
		$func = "get_$what";
		$data = $this->$func();
		View::json($data)->output();
	}


	private function get_fresh()
	{
		return Model::freshFiles()->find();
	}
}
