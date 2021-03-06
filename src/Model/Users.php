<?php

namespace Model;

use Data\User;
use Session, DB, Valid;

/**
 * User model for handling logins, etc.
 */
class Users extends \Model
{
	const SESSION_KEY = 'user';


	/**
	 * Try login user.
	 */
	public function login(array $data): User
	{		
		extract($_POST, EXTR_SKIP);

		// Check if user exists
		$user = $this->get($email ?? null);
		if( ! $user)
			throw new \Error\UnknownLogin();

		// Check password
		if( ! $user->verify_password($password ?? null))
			throw new \Error\UnknownLogin();

		// Login
		return $this->_login($user);
	}



	/**
	 * Login via link.
	 */
	public function login_token(array $data): User
	{
		if( ! Valid::keys_exist($data, ['email', 'token']))
			throw new \Error\UnknownResetToken();

		extract($data, EXTR_SKIP);

		// Check if user exists
		$user = $this->get($email);
		if( ! $user)
			throw new \Error\UnknownResetToken();

		// Check token
		if( ! $user->verify_token($token))
			throw new \Error\UnknownResetToken();

		// Login
		return $this->_login($user);
	}
	
	private function _login(User $user): User
	{
		Session::set(self::SESSION_KEY, $user->id());
		return $user;
	}



	/**
	 * Logout user.
	 */
	public function logout()
	{
		Session::unset(self::SESSION_KEY);
	}



	/**
	 * Get logged in user; false if not logged in.
	 */
	public function logged_in()
	{
		// Already checked this request
		if(self::$_user !== null)
			return self::$_user;

		// Supposed to be logged in
		$id = Session::get(self::SESSION_KEY);
		if( ! $id || ! is_int($id))
			return self::$_user = false;

		// User (still) exists?
		try
		{
			$user = self::$_user ?? $this->get($id);
		}
		catch(\Error\NotFound $e)
		{
			return self::$_user = false;
		}

		// Can (still) login?
		if( ! $user->has_roles(['login']))
			return self::$_user = false;

		// Return user
		return self::$_user = $user;
	}
	private static $_user = null;



	/**
	 * Get user by id or email.
	 */
	public function get($id = null): User
	{
		if($id === null || is_numeric($id))
			return User::get($id);
		
		$user = DB::prepare('SELECT * 
								FROM user 
								WHERE email=:email')
			->bindValue(':email', $id)
			->execute()
			->fetchFirst(User::class);

		if( ! $user)
			throw new \Error\NotFound($id, User::class);
		
		return $user;
	}


	/**
	 * Delete user by id.
	 */
	public function delete(int $id): bool
	{
		return User::delete($id);
	}


	/**
	 * Get all users.
	 */
	public function all(): array
	{
		return User::all();
	}

}
