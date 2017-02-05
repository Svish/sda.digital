<?php

namespace Error;

/**
 * 400 Unknown Login
 */
class NotFound extends HttpException
{
	public function __construct($id, $what)
	{
		parent::__construct([$id, $what], 400);
	}
}
