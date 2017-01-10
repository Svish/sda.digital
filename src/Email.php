<?php

/**
 * Email helper.
 */
class Email
{
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
	private function send(Swift_Message $message)
	{
		// Set common message stuff
		$text = $message->getBody();
		$html = Markdown::render($text);
		$message
			->setSender($this->config['smtp']['username'])
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
		return $mailer->send($message) > 0;
	}


	public function __construct()
	{
		$this->config = Config::contact();
	}


	public static function __callStatic($name, $args)
	{
		try
		{
			return call_user_func_array([new self, "send_$name"], $args);
		}
		catch(Swift_SwiftException $e)
		{
			error_log("Failed to send '$name' email: " . $e->getMessage());
			throw new HttpException('Failed to send email.', 500, $e);
		}
	}
}
