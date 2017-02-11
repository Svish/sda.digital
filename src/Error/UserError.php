<?php

namespace Error;
use Text;

/**
 * 4xx User errors.
 */
abstract class UserError extends HttpException
{
	public function __construct(int $code, $message = [], \Throwable $reason = null)
	{
		// If array, use as parameters and format $message
		if(is_array($message))
		{
			$class = get_class($this);
			$class = str_replace('Error\\', '', $class);
			$message = Text::exception($class, $message);
		}

		parent::__construct($code, $message, $reason);
	}
}
