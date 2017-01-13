<?php

/**
 * Takes care of securing stuff.
 */
abstract class SecureController extends SessionController
{
	protected $user;
	protected $required_roles = false;

	public function before(array &$info)
	{
		parent::before($info);

		// Get logged in user (if any)
		$this->user = Model::user()->logged_in(true);

		// Open to anyone if empty required_roles
		if($this->required_roles === false)
			return;

		// Check if logged in
		if( ! $this->user)
			// Redirect to login
			HTTP::redirect('user/login?url='.urlencode(ltrim($info['path'], '/')));

		// Check roles
		$this->required_roles[] = 'login';
		if( ! $this->user->has_roles($this->required_roles))
			throw new HttpException('Ingen tilgang', 403);
	}
}
