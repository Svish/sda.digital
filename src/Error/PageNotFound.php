<?php

namespace Error;

/**
 * 404 Page Not Found
 */
class PageNotFound extends UserError
{
	public function __construct($path = null, \Throwable $reason = null)
	{
		parent::__construct(404, [$path ?? PATH], $reason);
	}
}
