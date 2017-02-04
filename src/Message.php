<?php

/**
 * Wraps up and adds messages to session.
 *
 * @uses Text
 */
class Message
{
	const SESSION_KEY = 'messages';

	public static function exception(Exception $e)
	{
		return self::add('error', $e->getMessage());
	}

	public static function __callStatic($type, $args)
	{
		$key = array_shift($args);
		return self::add($type, Text::$type($key, $args));
	}

	private static function add($type, $text)
	{
		Session::append('messages', get_defined_vars());
	}
}
