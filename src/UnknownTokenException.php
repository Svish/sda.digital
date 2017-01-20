<?php


class UnknownTokenException extends HttpException
{
	public function __construct()
	{
		parent::__construct('Unknown token.', 400);
	}
}
