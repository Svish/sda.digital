<?php

namespace Controller;
use HTTP,Model;

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
	private $user;

	public function __construct()
	{
		parent::__construct();

		// Open to anyone if required_roles is false
		if($this->required_roles === false)
			return;

		// Get logged in user
		$this->user = Model::users()->logged_in();

		// Redirect if not logged in
		if( ! $this->user)
			HTTP::redirect('user/login?url='.urlencode(PATH));

		// Always require login role
		$this->required_roles[] = 'login';

		// Check roles
		if( ! $this->user->has_roles($this->required_roles))
			throw new \Error\NoAccess();
	}
}
