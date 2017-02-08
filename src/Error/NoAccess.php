<?php

namespace Error;

/**
 * 403 No Access
 */
class NoAccess extends UserError
{
	public function __construct()
	{
		parent::__construct(403);
	}
}
