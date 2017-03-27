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
		$back = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
		Log::trace("Called from {$back['class']}->{$back['function']}");

		// Get logged in user
		$user = Model::users()->logged_in();

		// Redirect if not logged in
		if( ! $user)
		{
			Log::info('User not logged in.');
			throw new \Error\Unauthorized();
		}

		// Always require login role
		array_unshift($roles, 'login');

		// Check roles
		if( ! $user->has_roles($roles))
		{
			Log::warn('Requires:', $roles, '; Has:', $user->roles);
			throw new \Error\Forbidden($roles);
		}

		return true;
	}


	/**
	 * Checks if logged in and has required roles.
	 *
	 * @throws Unauthorized If not logged in.
	 * @throws Forbidden If not having required roles
	 */
	public static function check(string ...$roles): bool
	{
		$user = Model::users()->logged_in();

		if( ! $user)
			return false;

		array_unshift($roles, 'login');
		if( ! $user->has_roles($roles))
			return false;

		return true;
	}
}
