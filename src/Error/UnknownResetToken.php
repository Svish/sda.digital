<?php

namespace Error;

/**
 * 400 Unknown Reset Token
 */
class UnknownResetToken extends HttpException
{
	public function __construct()
	{
		parent::__construct([], 400);
	}
}
