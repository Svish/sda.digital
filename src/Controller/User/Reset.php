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
		if(isset($_GET['email']))
		{
			try
			{
				Model::users()->login_token($_GET);
				Message::ok('reset_done');
				HTTP::redirect('user/me');
			}
			catch(\Error\UnknownResetToken $e)
			{
				return parent::error($e);
			}
		}

		return View::template()->output();
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
		Message::ok('reset_sent');
		HTTP::redirect_self();
	}
}
