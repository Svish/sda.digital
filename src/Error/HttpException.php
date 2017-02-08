<?php

namespace Error;
use HTTP;

/**
 * Exception with HTTP status and title.
 */
class HttpException extends \Exception
{
	protected $httpStatus;
	protected $httpTitle;

	public function __construct(int $httpStatus = 500, string $message = null, \Throwable $cause = null, $code = E_USER_ERROR)
	{
		$this->httpStatus = $httpStatus;
		$this->httpTitle = HTTP::status($httpStatus);
		parent::__construct($message ?? $this->httpTitle, $code, $cause);
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
