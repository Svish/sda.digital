<?php


/**
 * Dumb an object.
 */
class Helper_Debug
{
	public function __invoke($object)
	{
		var_dump($object);exit;
		return print_r($object, true);
	}
}
