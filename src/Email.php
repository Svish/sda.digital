<?php

/**
 * Email helper.
 */
class Email
{
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
		$message = Swift_Message::newInstance()
			->setFrom($this->config['smtp']['sender'])
			->setTo($to)
			->setSubject($subject)
			->setBody($message);

		return $this->send($message);
	}


	/**
	 * Send contact email $from someone.
	 */
	public function send_feedback($from, $subject, $message)
	{
		$message = Swift_Message::newInstance()
			->setTo($this->config['contact']['address'])
			->setFrom($from)
			->setSubject($subject)
			->setBody($message);

		return $this->send($message);
	}


	/**
	 * Send message.
	 */
	private function send(Swift_Message $message): bool
	{
		// Set common message stuff
		$text = $message->getBody();
		$html = Markdown::render($text);
		$message
			->setSender($this->config['smtp']['sender'])
			->setBody($html, 'text/html')
			->addPart($text, 'text/plain');

		// Create transport
		$transport = Swift_SmtpTransport::newInstance()
			->setEncryption('tls')
			->setHost($this->config['smtp']['server'])
			->setPort($this->config['smtp']['port'])
			->setUsername($this->config['smtp']['username'])
			->setPassword($this->config['smtp']['password']);

		// Send message
		$mailer = Swift_Mailer::newInstance($transport);
		$mailer->registerPlugin(Email\SwiftLogger::plugin());
		return $mailer->send($message) > 0;
	}


	public function __construct()
	{
		$this->config = Config::contact();
	}
}
