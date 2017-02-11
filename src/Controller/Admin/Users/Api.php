<?php

namespace Controller\Admin\Users;
use Model;

class Api extends \Controller\Api
{
	protected $required_roles = ['admin'];


	public function get_users()
	{
		return Model::users()->all();
	}


	public function get_new()
	{
		return Model::users()->get(null);
	}


	public function put_user($data)
	{
		$user = Model::users()->get($data['id'] ?? null);
		$user->set($data);
		$user->save();
		return $user;
	}


	public function delete_user($data)
	{
		Model::users()->delete($data);
	}
}
