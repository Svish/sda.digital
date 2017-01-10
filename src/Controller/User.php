<?php

/**
 * Handles login, logout and resetting of password.
 */
class Controller_User extends Controller_MultiPage
{
	/**
	 * Logout
	 */
	public function logout_get($url)
	{
		Model::user()->logout();
		HTTP::redirect();
	}





	/**
	 * Login
	 */
	public function login_post($url)
	{
		if(Model::user()->login($_POST))
		{
			$url = empty($_POST['url'])
				? 'admin'
				: $_POST['url'];
			HTTP::redirect($url);
		}

		HTTP::set_status(422);
		return parent::get($url, Msg::error('invalid_login'));
	}





	/**
	 * Reset token stuff.
	 */
	public function reset_get($url)
	{
		if(isset($_GET['email']))
		{
			if(Model::user()->login_token($_GET))
				HTTP::redirect('user/me?reset');
			else
				return parent::get($url, Msg::error('invalid_token'));
		}

		if(isset($_GET['sent']))
		{
			return parent::get($url, Msg::ok('reset_sent'));
		}

		return parent::get($url);
	}

	public function reset_post($url)
	{
		// Look for user
		$user = Model::user()->find($_POST['email']);
		if( ! $user)
		{
			HTTP::set_status(422);
			return parent::get($url, Msg::error('unknown_user'));
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
		if(Email::info($to, $subject, $message))
			HTTP::redirect('user/reset?sent');
		else
			throw new HttpException('Failed to send email', 500);
	}





	/**
	 * Me page.
	 */
	public function me_get($url, $context = [])
	{
		if( ! $this->user)
			HTTP::redirect('user/login?url='.urlencode(ltrim($info['path'], '/')));

		if(isset($_GET['saved']))
			return parent::get($url, Msg::ok('saved'));

		if(isset($_GET['reset']))
			return parent::get($url, Msg::ok('reset_done'));

		return parent::get($url, $context);
	}

	public function me_post($url)
	{
		if( ! $this->user)
			HTTP::redirect('user/login?url='.urlencode(ltrim($info['path'], '/')));

		try
		{
			$this->user
				->set($_POST)
				->save();
			HTTP::redirect('user/me?saved');
		}
		catch(ValidationException $e)
		{
			return parent::get($url, $this->error('save_fail', $e));
		}
	}
}
