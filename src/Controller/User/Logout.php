<?php

/**
 * Handles user logout.
 */
class Controller_User_Logout extends Controller_Page
{
	public function get($url = null, $context = [])
	{
		Model::user()->logout();
		HTTP::redirect();
	}
}
