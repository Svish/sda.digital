<?php

use Email\Smtp;
use Email\Message;

/**
 * Email helper.
 * 
 * @uses Config email
 */
class Email
{
	public function send_reset(\Data\User $user)
	{
		// Make token
		$user->make_token();

		// Create email (using first line as subject)
		$text = Mustache::engine()->render('user/reset-email',
			[
				'user' => $user,
				'host' => HOST,
				'u' => new \View\Helper\Url,
			]);
		$text = preg_split('/\R/', $text);

		$to = [$user->email => $user->name];
		$subject = array_shift($text);
		$message = trim(implode("\r\n", $text));

		$this->send_info($to, $subject, $message);
	}

	/**
	 * Send info $to someone.
	 */
	public function send_info($to, $subject, $message)
	{
		$message = (new Message)
			->setFrom($this->config['smtp']['sender'])
			->setTo($to)
			->setSubject($subject)
			->setBodyMd($message);

		return $this->send($message);
	}


	/**
	 * Send contact email $from someone.
	 */
	public function send_feedback($from, $subject, $message)
	{
		$message = (new Message)
			->setTo($this->config['contact']['address'])
			->setFrom($from)
			->setSubject($subject)
			->setBodyMd($message);

		return $this->send($message);
	}





	public function __construct()
	{
		$this->config = Config::email();
	}



	private function send(Message $message): bool
	{
		return Smtp::send($message);
	}



	public static function __callStatic($name, $args)
	{
		try
		{
			Log::group();
			Log::trace_raw("Sending $name email…");
			$result = call_user_func_array([new self, "send_$name"], $args);
			Log::groupEnd();
			return $result;
		}
		catch(Swift_SwiftException $e)
		{
			throw new Exception('Failed to send email.', $e);
		}
	}


}
