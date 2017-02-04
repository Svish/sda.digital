<?php

/**
 * Handles sending of contact emails.
 */
class Controller_Contact extends Controller_Page
{
	private $_rules = [
		'from' => ['not_empty', 'email', 'email_domain'],
		'subject' => ['not_empty'],
		'message' => ['not_empty'],
		];
	
	
	public function get()
	{
		$context = isset($_GET['sent'])
			? Msg::ok('email_sent')
			: [];

		return TemplateView::output($context);
	}


	public function post()
	{
		try
		{
			// Check
			Valid::check($_POST, $this->_rules);

			// Send
			Email::feedback($_POST['from'], $_POST['subject'], $_POST['message']);
			
			// Redirect
			HTTP::redirect_self('?sent');
		}
		catch(HttpException $e)
		{
			return parent::error($e);
		}
	}
}
