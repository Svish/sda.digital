<?php

namespace Error;

/**
 * 400 Unknown Login
 */
class UnknownLogin extends UserError
{
	public function __construct(\Throwable $e = null)
	{
		parent::__construct(400, [], $e);
	}
}
