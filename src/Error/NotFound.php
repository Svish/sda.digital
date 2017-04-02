<?php

namespace Error;

/**
 * 404 Not found
 *
 * Used for e.g. database entities, not pages.
 */
class NotFound extends UserError
{
	public function __construct($id, $what)
	{
		parent::__construct(404, [$id, $what]);
	}
}
