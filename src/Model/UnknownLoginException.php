<?php

namespace Model;
use HttpException;

class UnknownLoginException extends HttpException
{
	public function __construct()
	{
		parent::__construct([], 400);
	}
}
