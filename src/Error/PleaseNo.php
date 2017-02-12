<?php

namespace Error;

/**
 * 403 Forbidden
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
