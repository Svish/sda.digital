<?php

namespace View\Helper;
use Model, Security;


/**
 * Outputs only if user has role.
 */
class Role
{
	public function __isset($key)
	{
		return true;
	}

	private $roles = [];
	public function __get($key)
	{
		return $this->roles[$key]
			?? $this->roles[$key] = new self($key);
	}

	private $role;
	public function __construct($role)
	{
		if( ! is_string($role))
			return;

		$this->role = $role;
	}

	public function __invoke($text, \Mustache_LambdaHelper $render = null)
	{
		try
		{
			Security::require([$this->role]);
			return $render ? $render($text) : $text;
		}
		catch(\Error\Forbidden $e)
		{
			return null;
		}
	}
}
