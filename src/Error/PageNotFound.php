<?php

namespace Error;

/**
 * 404 Page Not Found
 */
class PageNotFound extends HttpException
{
	public function __construct($path, \Throwable $reason = null)
	{
		parent::__construct([$path], 404, $reason);
	}
}
