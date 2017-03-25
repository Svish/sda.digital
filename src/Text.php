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

		$text = self::$t[$header][$key] ?? "$header.$key";

		if(is_array($text))
			$text = implode("\r\n", $text);

		return $args
			? vsprintf($text, $args)
			: $text;
	}
}
