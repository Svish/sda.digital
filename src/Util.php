<?php

class Util
{
	/**
	 * @return slug version of $s
	 */
	public static function slug($s)
	{
		$s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
		$s = preg_replace(['/\s+/', '/[^-\w.]+/'], ['-', ''], $s);
		return strtolower($s);
	}

	/**
	 * #rrggbb or #rgb to [r, g, b]
	 */
	public static function hex2rgb($hex)
	{
		$hex = ltrim($hex, '#');

		if(strlen($hex) == 3)
			return [
				hexdec($hex[0].$hex[0]),
				hexdec($hex[1].$hex[1]),
				hexdec($hex[2].$hex[2]),
			];
		else
			return [
				hexdec($hex[0].$hex[1]),
				hexdec($hex[2].$hex[3]),
				hexdec($hex[4].$hex[5]),
			];
	}

	/**
	 * [r, g, b] to #rrggbb
	 */
	public static function rgb2hex(array $rgb)
	{
		return '#'
			. sprintf('%02x', $rgb[0])
			. sprintf('%02x', $rgb[1])
			. sprintf('%02x', $rgb[2]);
	}



	/**
	 * String starts with.
	 */
	public static function starts_with($haystack, $needle)
	{
		return $needle === "" || strpos($haystack, $needle) === 0;
	}

	/**
	 * String ends with.
	 */
	public static function ends_with($haystack, $needle)
	{
		return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}



	/**
	 * Returns an array containing only the whitelisted keys.
	 */
	public static function array_whitelist(array $array, array $whitelist)
	{
		return array_intersect_key($array, array_flip($whitelist));
	}

	/**
	 * Returns an array containing none of the blaclisted keys.
	 */
	public static function array_blacklist(array $array, array $blacklist)
	{
		return array_diff_key($array, array_flip($blacklist));
	}
}
