<?php

namespace Controller\User;
use HTTP, Model;

/**
 * Handles user login.
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
			try
			{
				Model::users()->login($_POST);
			}
			catch(\Error\NotFound $e)
			{
				throw new \Error\UnknownLogin($e);
			}

			$url = $_POST['url'] ?? 'admin';
			if(HTTP::is_local($url))
				HTTP::redirect($url);
			else
				HTTP::redirect('admin');
		}
		catch(\Error\HttpException $e)
		{
			return parent::error($e);
		}
	}
}
