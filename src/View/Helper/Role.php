<?php

namespace View\Helper;
use Model;


/**
 * Outputs only if user has role.
 */
class Role
{
	public function __isset($key)
	{
		return true;
	}
	public function __get($key)
	{
		return new self($key);
	}


	private $role;
	public function __construct($role)
	{
		$this->role = $role;
	}

	public function __invoke($text, \Mustache_LambdaHelper $render = null)
	{
		$user = Model::users()->logged_in();
		if( ! $user)
			return null;

		if( ! $user->has_roles([$this->role]))
			return null;

		return $render ? $render($text) : $text;
	}
}
