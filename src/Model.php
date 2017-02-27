<?php

/**
 * Base for model classes.
 */
abstract class Model
{
	public static function __callStatic($name, $args)
	{
		$name = __CLASS__.'\\'.ucfirst($name);
		return new $name(...$args);
	}
}
