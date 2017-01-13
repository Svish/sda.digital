<?php

/**
 * Handles user account.
 */
class Controller_User_Reset extends Controller_Page
{
	public function get($url = null, $context = [])
	{
		if(isset($_GET['sent']))
		{
			return parent::get($this->path, Msg::ok('reset_sent'));
		}

		if(isset($_GET['email']))
		{
			if(Model::user()->login_token($_GET))
				HTTP::redirect('user/me?reset');
			
			HTTP::set_status(400);
			return parent::get($this->path, Msg::error('unknown_token'));
		}

		return parent::get($this->path);
	}


	public function post()
	{
		// Look for user
		$user = Model::user()->get($_POST['email']);
		if( ! $user)
		{
			HTTP::set_status(422);
			return parent::get($this->path, Msg::error('unknown_user'));
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
		try
		{
			Email::info($to, $subject, $message);
			HTTP::redirect('user/reset?sent');
		}
		catch(HttpException $e)
		{
			return parent::get($this->path, $this->error('email_fail', $e));
		}
	}
}
