<?php

namespace DB;
use DB, Data\Sql;
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
				$value = array_map('trim', $value);
				$allowed = explode(',', $m);
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

	public static function unique($value, string $property, Sql $subject)
	{
		// Get primary key(s)
		$pk = $subject->pk();

		// Get primary key(s) of row with $property = $value
		$cols = implode(', ', array_keys($pk));
		$table = $subject::table_name();

		$found = DB::prepare("SELECT $cols 
				FROM $table
				WHERE $property = ?")
			->execute([$value])
			->fetchFirstArray();


		// If $subject has $pk (is in db)
		if( ! empty(array_filter($pk)))
			// Unique if $found and $pk match (found self)
			return $found == $pk;

		// Otherwise, unique if not $found
		return ! $found;
	}
}
