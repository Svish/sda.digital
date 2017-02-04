<?php

namespace Controller\User;
use HTTP, Model;
use HttpException;

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
			Model::users()->login($_POST);
			$url = $_POST['url'] ?? 'admin';

			if(HTTP::is_local($url))
				HTTP::redirect($url);
			else
				HTTP::redirect('admin');
		}
		catch(HttpException $e)
		{
			return $this->error($e);
		}
	}
}
