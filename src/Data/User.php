<?php
namespace Data;

class User extends Sql
{
	const SERIALIZE = ['id', 'email', 'name', 'roles'];
	const RESTRICTED = ['roles' => ['admin']];

	protected $_rules = [
			'email' => ['email', 'email_domain'],
		];

	protected $_password_rules = [
			'password' => ['not_empty', ['min_length', 12]],
		];



	public function __set($key, $value)
	{
		parent::__set($key, $value);

		if($value)
		switch($key)
		{
				case 'password':
					$this->_rules += $this->_password_rules;
					
				case 'token':
					$hash = password_hash($value, self::ALGO, self::ALGO_OPT);
					parent::__set("{$key}_hash", $hash);
					break;
		}
	}


	
	public function __unset($key)
	{
		parent::__unset($key);

		// Also unset hash
		switch($key)
		{
			case 'password':
			case 'token':
				parent::__unset("{$key}_hash");
				break;
		}
	}



	public function has_roles(array $roles): bool
	{
		$has = explode(',', $this->roles);

		foreach($roles as $role)
			if( ! in_array($role, $has))
				return false;

		return true;
	}



	public function verify_password(string $password): bool
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



	public function make_token(): self
	{
		$this->token = bin2hex(random_bytes(16));
		$this->save();
		return $this;
	}

	

	public function verify_token(string $token): bool
	{
		$result = password_verify($token, $this->token_hash);

		// TODO: Add a TTL for valid tokens
		if($result)
		{
			unset($this->token);
			$this->save();
		}

		return $result;
	}



	const ALGO = PASSWORD_DEFAULT;
	const ALGO_OPT = [];
}
