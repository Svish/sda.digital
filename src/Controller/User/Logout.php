<?php

namespace Controller\User;
use HTTP, Model;

/**
 * Handles user logout.
 */
class Logout extends \Controller\Page
{
	public function get($url = null, $context = [])
	{
		Model::users()->logout();
		HTTP::redirect();
	}
}
