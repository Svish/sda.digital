<?php

namespace View\Helper;
use Model, Security;


/**
 * Helper: Output wrapped text only if user has role.
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
		if($role instanceof \View)
			return;


		$this->role = $role;
	}

	public function __invoke($text = null, \Mustache_LambdaHelper $render = null)
	{
		if(Security::check($this->role ?: 'login'))
			return $render ? $render($text) : $text;
		
		return null;
	}
}
