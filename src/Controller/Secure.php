<?php

namespace Controller;
use HTTP,Model;

/**
 * Base for secure controllers.
 *
 * $required_roles = false => Open to everyone
 * $required_roles = [] => Require login
 * $required_roles = ['foo', 'bar'] => Require foo, bar and login
 */
abstract class Secure extends Session
{
	protected $required_roles = false;

	public function __construct()
	{
		parent::__construct();

		// Open to anyone if required_roles is false
		if($this->required_roles === false)
			return;

		self::access($this->required_roles);
	}

	/**
	 * Checks if logged in and has required roles.
	 */
	public static function access(array $roles)
	{
		// Get logged in user
		$user = Model::users()->logged_in();

		// Redirect if not logged in
		if( ! $user)
			throw new \Error\Unauthorized();

		// Always require login role
		$roles[] = 'login';

		// Check roles
		if( ! $user->has_roles($roles))
			throw new \Error\NoAccess();
	}
}
