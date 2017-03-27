<?php

/**
 * Gets and formats text from the text.ini file.
 *
 * Examples: 
 *     Text::ok('email-sent');
 *     Text::error('within', [1, 10]);
 */
class Text
{
	private static $t;

	public static function __callStatic($header, $args)
	{
		if( ! self::$t)
			self::$t = Config::text();

		$key = array_shift($args);
		$key = is_array($key) ? 'Array' : strval($key);
		$args = array_shift($args);

		$text = self::$t[$header][$key] ?? $key;

		if(is_array($text))
			$text = implode("\r\n", $text);

		if(is_array($args))
			$args = array_map(['self', 'implode'], $args);

		return $args
			? vsprintf($text, $args)
			: $text;
	}

	private static function implode($item)
	{
		return is_array($item)
			? json_encode($item)
			: $item;
	}
}
