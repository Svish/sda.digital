<?php

/**
 * Takes care of securing stuff.
 */
abstract class SecureController extends SessionController
{
	protected $user;
	protected $require_roles = null;

	public function before(array &$info)
	{
		parent::before($info);

		// Get logged in user (if any)
		$this->user = Model::user()->logged_in(true);

		// Open to anyone if empty require_roles
		if( ! $this->require_roles)
			return;

		// Check logged in
		if( ! $this->user)
			// Redirect to login
			HTTP::redirect('user/login?url='.urlencode(ltrim($info['path'], '/')));

		// Check roles
		if( ! $this->user->has_roles($this->require_roles))
			throw new HttpException('Ingen tilgang', 403);
	}
}
