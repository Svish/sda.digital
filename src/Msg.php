<?php

/**
 * Helper class for wrapping up a text as a message.
 *
 * @uses Text
 */
class Msg
{
	public static function exception(Exception $e)
	{
		return self::get('error', $e->getMessage());
	}

	public static function __callStatic($type, $args)
	{
		$key = array_shift($args);
		return self::get($type, Text::$type($key, $args));
	}

	public static function get($type, $text)
	{
		return ['message' => get_defined_vars()];
	}
}
