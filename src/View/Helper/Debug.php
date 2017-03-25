<?php

namespace View\Helper;

/**
 * Helper: Dumb an object.
 */
class Debug
{
	public function __invoke($object)
	{
		var_dump($object);exit;
		return print_r($object, true);
	}
}
