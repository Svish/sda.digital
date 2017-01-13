<?php

class Data_User extends SqlData
{
	protected $rules = [
			'email' => ['email', 'email_domain'],
		];



	public function __set($key, $value)
	{
		parent::__set($key, $value);

		switch($key)
		{
			// Hash password and token
			case 'password':
				// Add rule if setting password
				if($value)
					$this->rules += ['password' => [['min_length', 12]]];
				
			case 'token':
				$hash = password_hash($value, self::ALGO, self::ALGO_OPT);
				parent::__set("{$key}_hash", $hash);
				break;
		}
	}

	
	public function __unset($key)
	{
		parent::__unset($key);
		switch($key)
		{
			// Also unset hash
			case 'password':
			case 'token':
				parent::__unset("{$key}_hash");
				break;
		}
	}



	public function has_roles(array $roles)
	{
		$has = explode(',', $this->roles);

		foreach($roles as $role)
			if( ! in_array($role, $has))
				return false;

		return true;
	}



	public function verify_password($password)
	{
		// Verify password
		if( ! password_verify($password, $this->password_hash))
			return false;

		// Rehash if necessary
		if(password_needs_rehash($this->password_hash, self::ALGO, self::ALGO_OPT))
		{
			$this->password = $password;
			$this->save();
		}

		return true;
	}



	public function make_token()
	{
		$this->token = bin2hex(random_bytes(16));
		$this->save();
	}

	

	public function verify_token($token)
	{
		$result = password_verify($token, $this->token_hash);

		unset($this->token);
		$this->save();

		return $result;
	}



	const ALGO = PASSWORD_DEFAULT;
	const ALGO_OPT = [];
}
