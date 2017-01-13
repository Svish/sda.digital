<?php


/**
 * User model for handling logins, etc.
 */
class Model_User extends Model
{
	const SESSION_KEY = 'user';


	/**
	 * Try login user.
	 */
	public function login(array $data)
	{
		if( ! Valid::keys_exist($data, ['email', 'password']))
			return false;
		extract($_POST, EXTR_SKIP);

		// Check if user exists
		$user = $this->get($email);
		if( ! $user)
			return false;

		// Check password
		if( ! $user->verify_password($password))
			return false;

		// Check role
		if( ! $user->has_roles(['login']))
			return false;

		// Login
		return $this->_login($user);
	}



	/**
	 * Login via link.
	 */
	public function login_token(array $data)
	{
		if( ! Valid::keys_exist($data, ['email', 'token']))
			return false;

		extract($data, EXTR_SKIP);

		// Check if user exists
		$user = $this->get($email);
		if( ! $user)
			return false;

		// Check token
		if( ! $user->verify_token($token))
			return false;

		// Check role
		if( ! $user->has_roles(['login']))
			return false;

		// Login
		return $this->_login($user);
	}



	private function _login(Data_User $user)
	{
		Session::set(self::SESSION_KEY, $user->id);
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
	public function logged_in($return_user = false)
	{
		$id = Session::get(self::SESSION_KEY);

		if($id === null)
			return;
		
		return $return_user 
			? $this->get($id)
			: $id;
	}



	/**
	 * Get user by id or email.
	 */
	public static function get($id)
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
