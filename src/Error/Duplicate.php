<?php

namespace Error;

/**
 * 400 Key constraint
 */
class Duplicate extends UserError
{
	public function __construct(\PdoException $e)
	{
		parent::__construct(400, $e->errorInfo, $e);
	}
}
