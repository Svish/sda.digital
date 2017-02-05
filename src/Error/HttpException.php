<?php

namespace Error;
use HTTP, Text;

/**
 * Exception with HTTP status and title.
 */
class HttpException extends \Exception
{
	protected $httpStatus;
	protected $httpTitle;

	public function __construct($message, $httpStatus = 500, \Throwable $cause = null, $code = E_USER_ERROR)
	{
		if(is_array($message))
		{
			$class = get_class($this);
			$class = str_replace('Error\\', '', $class);
			$message = Text::exception($class, $message);
		}

		$this->httpStatus = $httpStatus;
		$this->httpTitle = HTTP::status($httpStatus);
		parent::__construct($message, $code, $cause);
	}

	public function getHttpStatus()
	{
		return $this->httpStatus;
	}
	public function getHttpTitle()
	{
		return $this->httpTitle;
	}
}
