<?php


/**
 * User model for handling logins, etc.
 */
class Model_Users extends Model
{
	const SESSION_KEY = 'user';


	/**
	 * Try login user.
	 */
	public function login(array $data)
	{		
		extract($_POST, EXTR_SKIP);

		// Check if user exists
		$user = $this->get($email ?? null);
		if( ! $user)
			throw new UnknownLoginException();

		// Check password
		if( ! $user->verify_password($password ?? null))
			throw new UnknownLoginException();

		// Check role
		if( ! $user->has_roles(['login']))
			throw new UnknownLoginException();

		// Login
		return $this->_login($user);
	}



	/**
	 * Login via link.
	 */
	public function login_token(array $data)
	{
		if( ! Valid::keys_exist($data, ['email', 'token']))
			throw new UnknownTokenException();

		extract($data, EXTR_SKIP);


		// Check if user exists
		$user = $this->get($email);
		if( ! $user)
			throw new UnknownTokenException();

		// Check token
		if( ! $user->verify_token($token))
			throw new UnknownTokenException();

		// Check role
		if( ! $user->has_roles(['login']))
			throw new UnknownTokenException();

		// Login
		return $this->_login($user);
	}



	private function _login(Data_User $user)
	{
		Session::set(self::SESSION_KEY, (int) $user->id);
		return true;
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
		// Supposed to be logged in
		$id = Session::get(self::SESSION_KEY);
		if($id === null)
			return false;

		// User (still) exists?
		$user = self::$_logged_in ?? $this->get($id);
		if( ! $user)
			return false;

		// User (still) has login role?
		if( ! $user->has_roles(['login']))
			return false;

		// Store and return
		self::$_logged_in = $user;
		return $user;
	}
	private static $_logged_in;



	/**
	 * Get user by id or email.
	 */
	public function get($id)
	{
		if(is_int($id))
		return DB::prepare('SELECT * 
								FROM user 
								WHERE id=:id')
			->bindValue(':id', $id)
			->execute()
			->fetchFirst(Data_User::class);

		else
		return DB::prepare('SELECT * 
								FROM user 
								WHERE email=:email')
			->bindValue(':email', $id)
			->execute()
			->fetchFirst(Data_User::class);
	}

}
