<?php

class Security
{
	/**
	 * Checks if logged in and has required roles.
	 *
	 * @throws Unauthorized If not logged in.
	 * @throws Forbidden If not having required roles
	 */
	public static function require(array $roles): bool
	{
		// Get logged in user
		$user = Model::users()->logged_in();

		// Redirect if not logged in
		if( ! $user)
			throw new \Error\Unauthorized();

		// Always require login role
		array_unshift($roles, 'login');

		// Check roles
		if( ! $user->has_roles($roles))
			throw new \Error\Forbidden($roles);

		return true;
	}
}
