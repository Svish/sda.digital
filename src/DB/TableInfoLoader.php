<?php

namespace DB;
use PDO, Reflect;

/**
 * Performs DB migrations.
 */
class TableInfoLoader
{
	const DIR = SRC.DIRECTORY_SEPARATOR.'_schema'.DIRECTORY_SEPARATOR;


	private $pdo;
	private $database;
	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}


	public function __invoke()
	{
		// Get database name
		$this->database = $this->pdo
			->query("SELECT database()")
			->fetchColumn(0);

		// Get class map
		foreach(Reflect::descendents('Data\\RelationalSql', 'Data') as $class)
		{
			$table = call_user_func([$class, 'table_name']);
			$classes[$table] = $class;
		}


		// Get table info
		$tables = $this->get_tables();
		$relations = $this->get_relations($classes);
		$tables = array_merge_recursive($tables, $relations);

		// Yield as objects
		foreach($tables as $name => $info)
			yield $name => (object) $info;
	}





	/**
	 * $[table][column_names]  => [names]
	 * $[table][columns][column] => [info]
	 * $[table][rules][column] => [rules]
	 */
	private function get_tables(): array
	{
		$columns = $this->pdo
			->query("SELECT 
						table_name 'table',
						column_name 'column',
						column_key 'key',
						column_type 'type',
						CASE is_nullable WHEN 'YES' THEN true ELSE false END AS 'nullable',
						CASE extra WHEN 'auto_increment' THEN true ELSE false END AS 'auto_increment',
						CASE column_key WHEN 'UNI' THEN true ELSE false END AS 'unique',
						CASE WHEN column_default IS NULL THEN false ELSE true END AS 'has_default'
						FROM 
							information_schema.columns
						WHERE
							table_schema = '{$this->database}'
							AND table_name != 'version'
						")
			->fetchAll(PDO::FETCH_ASSOC);

		foreach($columns as $col)
		{
			extract($col);

			// Clean type
			$type = str_replace('\'', '', $type);
			
			// Set column names
			$tables[$table]['column_names'][] = $column;
			
			// Set column info
			$tables[$table]['columns'][$column] = [
				'unique' => (bool) $unique,
				'nullable' => (bool) $nullable,
				'auto_increment' => (bool) $auto_increment,

				'key' => $key,

				'pdo_type' => self::pdo_type($type),
				'db_type' => $type,
			];

			// Add primary keys
			if($key == 'PRI')
				$tables[$table]['primary_keys'][] = $column;

			// Add DB type rule
			$tables[$table]['rules'][$column][]
				= [['DB\\Valid', 'type'], $type];

			// Add nullable rule
			if( ! $nullable && ! $auto_increment)
				$tables[$table]['rules'][$column][] = 'not_empty';

			// Add unique rule
			if($unique)
				$tables[$table]['rules'][$column][]
				= [['DB\\Valid', 'unique']];
		}

		// Set simple queries for Sql::__call(Static)
		foreach($tables as $name => &$table)
		{
			$cols = array_map(function($col)
				{
					return "$col = ?";
				}, $table['primary_keys']);
			$cols = implode(' AND ', $cols);

			$table['query'] = [
				'get' => "SELECT * FROM $name WHERE $cols",
				'delete' => "DELETE FROM $name WHERE $cols",
			];
		}

		return $tables;
	}




	/**
	 * $[table][relations][property] => [info]
	 */
	private function get_relations(array $class_map): array
	{
		// Get second degree relations
		$second = $this->pdo
			->query("SELECT
						GROUP_CONCAT(referenced_table_name SEPARATOR ',') 'tables',
						GROUP_CONCAT(referenced_column_name SEPARATOR ',') 'columns',
						table_name 'through'
						FROM (
							SELECT 
								table_name,
								column_name,
								referenced_table_name,
								referenced_column_name
							FROM
								information_schema.key_column_usage
							WHERE
								referenced_table_name IS NOT NULL
								AND table_schema = '{$this->database}'
							) as query
					GROUP BY table_name
					HAVING count(referenced_table_name) > 1
				")
			->fetchAll(PDO::FETCH_ASSOC);


		// Gather M:M relations
		$mmt = [];
		foreach($second as $rel)
		{
			extract($rel);
			$mmt[] = $through;
			$table = explode(',', $tables);
			$column = explode(',', $columns);
			$relations[$table[0]]['relations']["{$table[1]}_list"] = [
				'type' => 'M:M',
				'class' => $class_map[$table[1]] ?? 'stdClass',
				'column' => $column[0],
				'query' => "SELECT {$table[1]}.*
					FROM {$table[1]}
					INNER JOIN $through USING ({$column[1]}) 
					WHERE $through.{$column[0]} = :{$column[0]}",
			];

			$relations[$table[1]]['relations']["{$table[0]}_list"] = [
				'type' => 'M:M',
				'class' => $class_map[$table[0]] ?? 'stdClass',
				'column' => $column[1],
				'query' => "SELECT {$table[0]}.*
					FROM {$table[0]}
					INNER JOIN $through USING ({$column[0]}) 
					WHERE $through.{$column[1]} = :{$column[1]}",
			];
		}

		// Get first degree relations
		$first = $this->pdo
			->query("SELECT DISTINCT
						k.referenced_table_name 'referenced_table',
						k.referenced_column_name 'referenced_column',
						k.table_name 'table',
						k.column_name 'column',
						c.column_key 'key'
					FROM information_schema.key_column_usage k
						JOIN information_schema.columns c USING (table_name, column_name)
						
					WHERE k.table_schema = '{$this->database}'
						AND k.referenced_table_name IS NOT NULL
					")
			->fetchAll(PDO::FETCH_ASSOC);

		// Gather M:1, 1:1 and 1:M relations
		foreach($first as $rel)
		{
			extract($rel);

			// Skip through-tables
			if(in_array($table, $mmt))
				continue;

			// Many-to-One / One-to-One
			$relations[$table]['relations'][$referenced_table] = [
				'type' => $key == 'UNI' ? '1:1' : 'M:1',
				'class' => $class_map[$referenced_table] ?? 'stdClass',
				'column' => $referenced_column,
				'query' => "SELECT $referenced_table.*
					FROM $referenced_table
					WHERE $referenced_column = :$referenced_column",
			];

			// One-to-Many / One-to-One
			$relations[$referenced_table]['relations'][$key == 'UNI' ? $table : "{$table}_list"] = [
				'type' => $key == 'UNI' ? '1:1' : '1:M',
				'class' => $class_map[$table] ?? 'stdClass',
				'column' => $column,
				'query' => "SELECT $table.*
					FROM $table
					WHERE $column = :$column",
			];
		}
		return $relations;
	}



	private static function pdo_type($type)
	{
		switch(preg_replace('/\(.+/', null, $type))
		{
			case 'tinyint':
			case 'smallint':
			case 'mediumint':
			case 'int':
			case 'bigint':
				return PDO::PARAM_INT;

			default:
				return PDO::PARAM_STR;
		}
	}
}
