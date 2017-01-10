<?php

class Util
{
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
	 * Yields the sub paths of $url.
	 *
	 * false => a/b/c, a/b, a
	 * true  => a/b/c, b/c, c
	 */
	public static function sub_paths($url, $backwards = false)
	{
		$url = explode('/', $url);
		while( ! empty($url))
		{
			yield implode('/', $url);
			if($backwards)
				array_shift($url);
			else
				array_pop($url);
		}
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



	/**
	 * @see https://github.com/kohana/core/blob/3.3/master/classes/Kohana/Arr.php#L89
	 */
	public static function path($array, $path, $default = NULL, $delimiter = '.')
	{
		if ( ! is_array($array))
		{
			return $default;
		}

		if (is_array($path))
		{
			$keys = $path;
		}
		else
		{
			if (array_key_exists($path, $array))
				return $array[$path];

			$path = ltrim($path, "{$delimiter} ");
			$path = rtrim($path, "{$delimiter} *");
			$keys = explode($delimiter, $path);
		}

		do
		{
			$key = array_shift($keys);

			if (ctype_digit($key))
				$key = (int) $key;

			if (isset($array[$key]))
			{
				if ($keys)
				{
					if (is_array($array[$key]))
						$array = $array[$key];
					else
						break;
				}
				else
				{
					return $array[$key];
				}
			}
			elseif ($key === '*')
			{
				$values = array();
				foreach ($array as $arr)
					if ($value = self::path($arr, implode('.', $keys)))
						$values[] = $value;

				if ($values)
					return $values;
				else
					break;
			}
			else
			{
				break;
			}
		}
		while ($keys);

		return $default;
	}



	/**
	 * @see https://github.com/kohana/core/blob/3.3/master/classes/Kohana/Arr.php#L437
	 */
	public static function merge($array1, $array2)
	{
		foreach ($array2 as $key => $value)
		{
			if (is_array($value)
			AND isset($array1[$key])
			AND is_array($array1[$key]) )
			{
				$array1[$key] = Util::merge($array1[$key], $value);
			}
			else
			{
				$array1[$key] = $value;
			}
		}

		return $array1;
	}
}
