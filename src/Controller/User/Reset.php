<?php

namespace Controller\User;
use HTTP, Model, Mustache, Email;
use Model\UnknownTokenException;

/**
 * Handles user account.
 */
class Reset extends Controller\Page
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
			catch(UnknownTokenException $e)
			{
				return parent::error($e);
			}
		}

		if(isset($_GET['sent']))
			Message::ok('reset_sent');

		return View::template()->output();
	}


	public function post()
	{
		// Look for user
		$user = Model::users()->get($_POST['email']);
		if( ! $user)
		{
			HTTP::set_status(422);
			Message::error('unknown_user');
			return View::template()->output();
		}


		// Make token
		$user->make_token();


		// Create email (using first line as subject)
		$text = Mustache::engine()->render('user/reset-email',
			[
				'user' => $user,
				'host' => HOST,
				'url' => new Helper_Url,
			]);
		$text = preg_split('/\R/', $text);

		$subject = array_shift($text);
		$message = trim(implode("\r\n", $text));


		// Send email
		$to = [$user->email => $user->name];
		Email::info($to, $subject, $message);
		HTTP::redirect('user/reset?sent');
	}
}
