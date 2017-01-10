<?php

class Data_User extends SqlData
{
	protected $rules = [
			'name' => ['not_empty'],
			'email' => ['not_empty', 'email', 'email_domain'],
		];


	public static function find($email)
	{
		return DB::prepare('SELECT * 
								FROM user 
								WHERE email=:email')
			->bindValue(':email', $email)
			->execute()
			->fetch(__CLASS__);
	}



	public function __set($key, $value)
	{
		parent::__set($key, $value);
		switch($key)
		{
			// Hash password and token
			case 'password':
				// Unset if empty
				if( ! $value)
					unset($this->password);
				// Add rule if setting password
				else
					$this->rules += ['password' => [['min_length', 12]]];
				
			case 'token':
				$key = "{$key}_hash";
				$this->$key = password_hash($value, self::ALGO, self::ALGO_OPT);
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
