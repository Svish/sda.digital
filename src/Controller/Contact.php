<?php

/**
 * Handles sending of contact emails.
 */
class Controller_Contact extends Controller_Page
{
	private $rules = [
		'from' => ['not_empty', 'email', 'email_domain'],
		'subject' => ['not_empty'],
		'message' => ['not_empty'],
		];
	
	
	public function get($url = null, $context = [])
	{
		if(isset($_GET['sent']))
			$context += Msg::ok('email_sent');

		parent::get($url, $context);
	}


	public function post($url)
	{
		try
		{
			// Check
			Valid::check($_POST, $this->rules);

			// Send
			Email::feedback($_POST['from'], $_POST['subject'], $_POST['message']);
			
			// Redirect
			HTTP::redirect_self('?sent');
		}
		catch(HttpException $e)
		{
			return parent::get($url, $this->error('email_fail', $e));
		}
	}
}
