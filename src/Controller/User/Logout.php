<?php

/**
 * Handles user logout.
 */
class Controller_User_Logout extends Controller_Page
{
	public function get($url = null, $context = [])
	{
		Model::users()->logout();
		HTTP::redirect();
	}
}
