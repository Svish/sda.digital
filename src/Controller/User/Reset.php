<?php

namespace Controller\User;
use HTTP, Model, View, Email, Message;

/**
 * Handles user account.
 */
class Reset extends \Controller\Page
{
	public function get()
	{
		if(isset($_GET['token']))
		{
			try
			{
				Model::users()->login_token($_GET);
				Message::ok('reset-done');
				HTTP::redirect('user/me', 303);
			}
			catch(\Error\UnknownResetToken $e)
			{
				return parent::error($e);
			}
		}

		return View::layout()->output();
	}


	public function post()
	{
		// Look for user
		try
		{
			try
			{
				$user = Model::users()->get($_POST['email']);
			}
			catch(\Error\NotFound $e)
			{
				throw new \Error\UnknownLogin($e);
			}
		}
		catch(\Error\UnknownLogin $e)
		{
			return parent::error($e);
		}

		// Send email
		Email::reset($user);
		Message::ok('reset-sent');
		HTTP::redirect_self();
	}
}
