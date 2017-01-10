<?php


/**
 * User model for handling logins, etc.
 */
class Model_User extends Model
{
	const SESSION_KEY = 'user';


	/**
	 * Get single user.
	 */
	public function find($email)
	{
		return Data::user()->find($email);
	}


	/**
	 * Try login user.
	 */
	public function login(array $data)
	{
		if( ! Valid::keys_exist($data, ['email', 'password']))
			return false;
		extract($_POST, EXTR_SKIP);

		// Check if user exists
		$user = $this->find($email);
		if( ! $user)
			return false;

		// Check password
		if( ! $user->verify_password($password))
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
		$user = $this->find($email);
		if( ! $user)
			return false;

		// Check token
		if( ! $user->verify_token($token))
			return false;

		// Login
		return $this->_login($user);
	}



	private function _login(Data_User $user)
	{
		$_SESSION[self::SESSION_KEY] = $user->email;
		return true;
	}



	/**
	 * Logout user.
	 */
	public function logout()
	{
		unset($_SESSION[self::SESSION_KEY]);
		return true;
	}



	/**
	 * Get logged in user; false if not logged in.
	 */
	public function logged_in($return_user = false)
	{
		if( ! array_key_exists(self::SESSION_KEY, $_SESSION))
			return false;

		$id = $_SESSION[self::SESSION_KEY];
		
		return $return_user 
			? $this->find($id) 
			: $id;
	}

}
