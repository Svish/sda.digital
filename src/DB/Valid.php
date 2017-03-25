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
		if( ! preg_match('/(?<type>\w+)(?:\((?<opts>[^)]+)\))?/m', $column_type, $type))
			return true;

		// Check
		extract($type);
		$opts = explode(',', $opts ?? '');


		switch($type)
		{
			// varchar(max_length)
			case 'varchar':
				return V::max_length($value, $opts[0]);

			// int
			case 'tinyint':
			case 'smallint':
			case 'int':
				return V::integer($value);

			// decimal(m,d)
			case 'decimal':
				// TODO: Validate M and D part
				return V::max_length($value, $opts[0]+1);

			// enum(allowed,values)
			case 'enum':
				return in_array($value, $opts);

			// set(allowed,values)
			case 'set':
				$value = explode(',', $value);
				$value = array_map('trim', $value);
				return $value == array_intersect($value, $opts);


			case 'datetime':
			case 'timestamp':
				return preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $value);

			case 'date':
				return preg_match('/\d{4}-\d{2}-\d{2}/', $value);

			case 'time':
				return preg_match('/\d{2}:\d{2}:\d{2}/', $value);


			default:
				return true;
		}
	}



	public static function unique($value, string $property, Sql $subject): bool
	{
		// Get primary key(s) of row with $property = $value
		$pk = $subject->pk();
		$cols = implode(', ', array_keys($pk));
		$table = $subject::table_name();

		$found = DB::prepare("SELECT $cols 
				FROM $table
				WHERE $property = ?")
			->execute([$value])
			->fetchFirstArray();

		if( ! $found)
			return true;

		return $subject->has_pk()
			? $found == $pk
			: ! $found;
	}
}
