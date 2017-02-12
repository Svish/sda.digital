<?php

namespace DB;
use Valid as V;

/**
 * Validation related to database
 */
class Valid
{
	public static function type($value, string $column_type): bool
	{
		// Ignore unmatching types
		if( ! preg_match('/(?<type>\w+)(?:\((?<m>[^)]+)\))?/m', $column_type, $column_type))
			return true;



		// Check
		extract($column_type);

		switch($type)
		{
			// varchar(max_length)
			case 'varchar':
				return V::max_length($value, $m);

			// set('allowed','values')
			case 'set':
				$value = explode(',', $value);
				$allowed = explode(',', str_replace('\'', '', $m));
				return $value == array_intersect($value, $allowed);

			case 'datetime':
			case 'timestamp':
				return preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $value);
			case 'time':
				return preg_match('/\d{2}:\d{2}:\d{2}/', $value);

			case 'date':
				return preg_match('/\d{4}-\d{2}-\d{2}/', $value);


			default:
				return true;
		}
	}

	public static function unique($value)
	{
		throw new Exception('Not implemented: '.__METHOD__);
		return true;
	}
}
