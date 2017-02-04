<?php

/**
 * Handles user login.
 */
class Controller_User_Login extends Controller_Page
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
			Model::users()->login($_POST);
			$url = $_POST['url'] ?? 'admin';

			if(HTTP::is_local($url))
				HTTP::redirect($url);
			else
				throw new HttpException('Invalid redirect URL', 400);
		}
		catch(UnknownLoginException $e)
		{
			return $this->error($e);
		}
	}
}
