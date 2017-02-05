<?php

namespace Error;

/**
 * 403 No Access
 */
class NoAccess extends HttpException
{
	public function __construct()
	{
		parent::__construct(403);
	}
}
