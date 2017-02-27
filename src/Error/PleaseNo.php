<?php

namespace Error;

/**
 * 403 Forbidden
 *
 * Things that could happen, but shouldn't,
 * unless the user is messing with something...
 */
class PleaseNo extends UserError
{
	public $actualReason;
	public function __construct(string $actualReason)
	{
		parent::__construct(403, []);
		$this->actualReason = $actualReason;
	}
}
