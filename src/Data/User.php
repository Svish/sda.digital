<?php
namespace Data;

class User extends RelationalSql
{
	const SERIALIZE = ['user_id', 'email', 'name', 'roles'];
	const RESTRICTED = ['roles' => ['admin']];


	public function __construct()
	{
		parent::__construct();
		$this->computed( new Hash('password', 'token') );
	}


	protected $rules = [
			'email' => ['email', 'email_domain'],
		];

	private $password_rules = [
			'password' => ['not_empty', ['min_length', 12]],
		];




	public function __set($key, $value)
	{
		parent::__set($key, $value);

		// Add rule when password set
		if($value && $key == 'password')
			$this->rules += $this->password_rules;
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
		if(password_needs_rehash($this->password_hash, Hash::ALGO, Hash::ALGO_OPT))
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
}
