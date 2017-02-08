<?php

namespace Error;

/**
 * 400 Unknown Login
 */
class NotFound extends UserError
{
	public function __construct($id, $what)
	{
		parent::__construct(400, [$id, $what]);
	}
}
