<?php

namespace DB;
use PDO;

/**
 * Performs DB migrations.
 */
class TableInfoLoader
{
	const DIR = SRC.DIRECTORY_SEPARATOR.'_schema'.DIRECTORY_SEPARATOR;


	private $pdo;
	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}


	public function __invoke()
	{
		// Get database name
		$db_name = $this->pdo
			->query("SELECT database()")
			->fetchColumn(0);

		// Get table names
		$tables = $this->pdo
			->query('SHOW TABLES')
			->fetchAll(PDO::FETCH_COLUMN, 0);

		// Get relations
		$relations = $this->pdo
			->query("SELECT 
						table_name,
						column_name,
						referenced_table_schema,
						referenced_table_name,
						referenced_column_name
					FROM
						information_schema.key_column_usage
					WHERE
  							table_schema = '$db_name'
						AND referenced_table_name IS NOT NULL
					")
			->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);

		// TODO: Analyze relations

		// Gather table info
		foreach($tables as $table)
		{
			// Fetch column info
			$columns = $this->pdo
				->query("SHOW COLUMNS FROM $table")
				->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);

			$info = (object)[
				'columns' => $columns,
				'column_names' => array_keys($columns), 
				'column_pdo_types' => [],
				'rules' => [],
				'auto_inc_column' => false,
				];

			// Add more info
			foreach ($columns as $name => $column)
			{
				// If auto_increment
				if($column['Extra'] == 'auto_increment')
					// Remember name for lastInsertId on save
					$info->auto_inc_column = $name;

				// If not, and not nullable
				elseif($column['Null'] == 'NO')
					// Add not_empty rule
					$info->rules[$name][] = 'not_empty';

				// Add db type rule
				$info->rules[$name][] = [['DB\\Valid', 'type'], str_replace('\'', '', $column['Type'])];

				// TODO: Add unique rule
				//if($column['Key'] == 'UNI')
					//$info->rules[$name][]

				// Add pdo type
				$info->column_pdo_types[$name] = self::pdo_type($column['Type']);
			}

			yield $table => $info;
		}
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
