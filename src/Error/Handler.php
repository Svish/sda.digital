<?php

namespace Error;
use View, HTTP, Message;

/**
 * Global error handler.
 */
class Handler
{
	public function __invoke(\Throwable $e = null)
	{
		// Wrap in Internal if not UserError
		if( ! $e instanceof UserError)
			$e = new Internal($e);

		// Add message
		Message::exception($e);

		// Redirect to login if unauthorized
		if($e instanceof Unauthorized)
			HTTP::redirect('user/login?url='.urlencode(PATH));


		// Set status
		HTTP::set_status($e);

		// Render error page
		$view = boolval(getallheaders()['Is-Ajax'] ?? false)
			? new Json($e)
			: new Html($e);
		$view->output();
		exit;
	}

	/**
	 * @see http://php.net/manual/en/class.errorexception.php#errorexception.examples
	 */
	public function error($severity, $message, $file, $line)
	{
		// Check if included in error_reporting
		if( ! (error_reporting() & $severity))
			return;

		$this(new \ErrorException($message, 0, $severity, $file, $line));
	}
}
