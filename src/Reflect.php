<?php

/**
 * Reflection helper.
 */
class Reflect
{
	/**
	 * Calls method on a new object, regardless of 
	 */
	public static function stealth_call(string $class, string $method, ...$args)
	{
		$obj = new \ReflectionClass($class);
		$method = $obj->getMethod($method);
		$method->setAccessible(true);

		$obj = $obj->newInstanceWithoutConstructor();
		return $method->invoke($obj, ...$args);
	}

	/**
	 * Creates an object of type $class, but calls $pre_ctor($obj) before
	 * actual constructor, PDO style.
	 */
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

	/**
	 * Simple psr-4 listing of non-vendor classes in namespace
	 */
	public static function classes_in_namespace(string $ns): Generator
	{
		$dir = SRC.$ns.DIRECTORY_SEPARATOR;
		foreach(glob("$dir*.php") as $file)
		{
			$class = substr($file, strlen(SRC), -4);
			if(class_exists($class))
				yield $class;
		}
	}

	/**
	 * Yields descendents of $class in $ns, as ReflectionClass objects.
	 */
	public static function descendents(string $class, string $ns): Generator
	{
		foreach(self::classes_in_namespace($ns) as $sub)
		{
			$c = new \ReflectionClass($sub);
			if($c->isSubclassOf($class))
				yield $sub;
		}
	}
}
