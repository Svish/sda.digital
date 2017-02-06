<?php

namespace Controller\User;
use HTTP, Model, Message;

/**
 * Handles user login.
 *
 * TODO: (?) http://stackoverflow.com/a/2093333/39321
 */
class Login extends \Controller\Page
{

	public function get()
	{
		$_POST['url'] = $_GET['url'] ?? 'admin';
		return parent::get();
	}
	

	public function post()
	{
		try
		{
			// Try login
			try
			{
				Model::users()->login($_POST);
				Message::ok('logged-in');
			}
			catch(\Error\NotFound $e)
			{
				throw new \Error\UnknownLogin($e);
			}

			// Redirect, but only if local
			$url = $_POST['url'] ?? 'admin';
			if(HTTP::is_local($url))
				HTTP::redirect($url, 303);
			else
				HTTP::redirect('admin', 303);
		}
		catch(\Error\HttpException $e)
		{
			return parent::error($e);
		}
	}

}
