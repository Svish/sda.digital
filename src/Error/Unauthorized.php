<?php

namespace Error;

/**
 * 401 Unauthorized
 */
class Unauthorized extends UserError
{
	public function __construct()
	{
		parent::__construct(401);
	}
}
