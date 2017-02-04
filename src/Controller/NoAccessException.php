<?php

namespace Controller;
use HttpException;

class NoAccessException extends HttpException
{
	public function __construct()
	{
		parent::__construct('No access', 403);
	}
}
