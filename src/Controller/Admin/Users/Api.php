<?php

namespace Controller\Admin\Users;
use Model;
use \Data\User;

class Api extends \Controller\Api
{
	protected $required_roles = ['admin'];


	public function get_users(): array
	{
		return Model::users()->all();
	}


	public function get_new(): User
	{
		return Model::users()->get(null);
	}


	public function put_user(array $data): User
	{
		$user = Model::users()->get($data['user_id'] ?? null);
		$user->set($data);
		$user->save();
		return $user;
	}


	public function delete_user(int $id)
	{
		Model::users()->delete($id);
	}
}
