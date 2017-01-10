<?php

/**
 * Helper class for wrapping up a text as a message.
 *
 * @uses Text
 */
class Msg
{
	public static function __callStatic($type, $args)
	{
		$key = array_shift($args);
		return self::get($type, Text::$type($key));
	}

	public static function get($type, $text)
	{
		return ['message' => get_defined_vars()];
	}
}
