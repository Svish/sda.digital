<?php

namespace Error;

/**
 * 403 No Access
 */
class NoAccess extends UserError
{
	public function __construct(array $required_roles)
	{
		parent::__construct(403, [implode(', ', $required_roles)]);
	}
}
