<?php

namespace Error;

/**
 * 400 Unknown Reset Token
 */
class UnknownResetToken extends UserError
{
	public function __construct()
	{
		parent::__construct(400);
	}
}
