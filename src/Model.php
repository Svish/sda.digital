<?php


/**
 * Base for model classes.
 */
abstract class Model
{
	/**
	 * Returns a new Model_$name.
	 */
	public static function __callStatic($name, $args)
	{
		$name = __CLASS__.'_'.ucfirst($name);
		$r = new ReflectionClass($name);
		return $r->newInstanceArgs($args);
	}


	/**
	 * Clears the cache for this class.
	 */
	public function clear_cache()
	{
		$cache = new Cache(get_class($this));
		$cache->clear();
	}
}
