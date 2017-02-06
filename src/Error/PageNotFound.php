<?php

namespace Error;

/**
 * 404 Page Not Found
 */
class PageNotFound extends HttpException
{
	public function __construct($path = null, \Throwable $reason = null)
	{
		parent::__construct([$path ?? PATH], 404, $reason);
	}
}
