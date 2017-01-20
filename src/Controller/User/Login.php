<?php

/**
 * Handles user login.
 */
class Controller_User_Login extends Controller_Page
{
	public function post()
	{
		try
		{
			Model::users()->login($_POST);
			$url = empty($_POST['url'])
				? 'admin'
				: $_POST['url'];
				
			HTTP::redirect($url);
		}
		catch(UnknownLoginException $e)
		{
			return $this->error($e);
		}
	}
}
