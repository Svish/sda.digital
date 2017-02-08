<?php

namespace Error;

/**
 * 500 Internal Server Error.
 *
 * Used for hiding non-UserError.
 */
class Internal extends HttpException
{
	public function __construct(\Throwable $reason = null)
	{
		parent::__construct(500, null, $reason);
	}
}
