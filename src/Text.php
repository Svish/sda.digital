<?php

/**
 * Gets text from the text.ini file.
 *
 * Examples: 
 *     Text::ok('email_sent');
 *     Text::error('between, [1, 10]);
 */
class Text
{
	private static $t;

	public static function __callStatic($header, $args)
	{
		if( ! self::$t)
			self::$t = Config::text();

		$key = array_shift($args);
		$text = self::$t[$header][''.$key] ?? "$header.$key";
		$args = array_shift($args);

		return $args
			? vsprintf($text, $args)
			: $text;
	}
}
