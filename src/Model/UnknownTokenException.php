<?php

namespace Model;
use HttpException;

class UnknownTokenException extends HttpException
{
	public function __construct()
	{
		parent::__construct([], 400);
	}
}
