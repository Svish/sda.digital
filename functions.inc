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

function array_group_by($key, $array, $keep_key = false, $group_key = 'items')
{
	$groups = [];
	foreach($array as $value)
	{
		$group = $value[$key] ?? '';

		if( ! $keep_key)
			unset($value[$key]);

		if( ! isset($groups[$group]))
			$groups[$group][$key] = $group;

		$groups[$group][$group_key][] = $value;
	}

	return array_values($groups);
}


function starts_with($needle, $haystack)
{
	return $needle === "" 
		|| strpos($haystack, $needle) === 0;
}

function ends_with($needle, $haystack)
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
