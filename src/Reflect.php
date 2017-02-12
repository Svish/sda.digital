<?php

/**
 * Reflection helper.
 */
class Reflect
{
	public static function pre_construct(string $class, callable $pre_ctor)
	{
		$obj = new \ReflectionClass($class);
		$ctor = $obj->getConstructor();
		$obj = $obj->newInstanceWithoutConstructor();

		$pre_ctor($obj);

		if($ctor)
			$ctor->invoke($obj);
		return $obj;
	}
}
