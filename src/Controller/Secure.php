<?php

namespace Controller;
use HTTP, Model, Security;

/**
 * Base for secure controllers.
 *
 * $required_roles = false => Open to everyone
 * $required_roles = [] => Require login
 * $required_roles = ['foo', 'bar'] => Require foo, bar and login
 */
abstract class Secure extends Session
{
	protected $required_roles = false;

	public function __construct()
	{
		parent::__construct();

		// Open to anyone if required_roles is false
		if($this->required_roles === false)
			return;

		Security::require($this->required_roles);
	}
}
