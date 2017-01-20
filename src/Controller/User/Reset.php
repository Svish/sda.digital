<?php

/**
 * Handles user account.
 */
class Controller_User_Reset extends Controller_Page
{
	public function get()
	{
		if(isset($_GET['sent']))
			return TemplateView::output(Msg::ok('reset_sent'));

		if(isset($_GET['email']))
		{
			try
			{
				Model::users()->login_token($_GET);
				HTTP::redirect('user/me?reset');
			}
			catch(UnknownTokenException $e)
			{
				return parent::error($e);
			}
		}

		return TemplateView::output();
	}


	public function post()
	{
		// Look for user
		$user = Model::users()->get($_POST['email']);
		if( ! $user)
		{
			HTTP::set_status(422);
			return TemplateView::output(Msg::error('unknown_user'));
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
