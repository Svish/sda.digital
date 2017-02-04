<?php

# Simple utility functions


function get_class_name($obj)
{
	return substr(strrchr('\\'.get_class($obj), '\\'), 1);
}

function array_whitelist(array $array, array $whitelist)
{
	return array_intersect_key($array, array_flip($whitelist));
}

function array_blacklist(array $array, array $blacklist)
{
	return array_diff_key($array, array_flip($blacklist));
}

function array_sort_by($key, array &$array)
{
	return usort($array, function($x, $y) use ($key)
	{
		return strnatcasecmp($x[$key] ?? null, $y[$key] ?? null);
	});
}

function starts_with($haystack, $needle)
{
	return $needle === "" 
		|| strpos($haystack, $needle) === 0;
}

function ends_with($haystack, $needle)
{
	return $needle === "" 
		|| substr($haystack, -strlen($needle)) === $needle;
}


/**
 * #rrggbb or #rgb to [r, g, b]
 */
function hex2rgb($hex)
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
function rgb2hex(array $rgb)
{
	return '#'
		. sprintf('%02x', $rgb[0])
		. sprintf('%02x', $rgb[1])
		. sprintf('%02x', $rgb[2]);
}
