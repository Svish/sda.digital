<?php

namespace Error;

/**
 * 403 Forbidden
 */
class Forbidden extends UserError
{
	public function __construct(array $required_roles)
	{
		parent::__construct(403, [implode(', ', $required_roles)]);
	}
}
