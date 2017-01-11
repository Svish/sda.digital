<?php

/**
 * Handles user login.
 */
class Controller_User_Login extends Controller_Page
{
	public function post()
	{
		if(Model::user()->login($_POST))
		{
			$url = empty($_POST['url'])
				? 'admin'
				: $_POST['url'];
			HTTP::redirect($url);
		}

		HTTP::set_status(400);
		return parent::get($this->path, Msg::error('unknown_login'));
	}
}
