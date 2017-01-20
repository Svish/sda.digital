<?php


class UnknownLoginException extends HttpException
{
	public function __construct()
	{
		parent::__construct('Unknown login.', 400);
	}
}
