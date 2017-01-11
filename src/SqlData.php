<?php

/**
 * Base class for data from SQL tables.
 */
abstract class SqlData extends Data
{
	protected $table_name;
	protected $rules;

	private $table_info;
	private $dirty;

	public function __construct()
	{
		// Reset dirty (from PDO constructor)
		$this->dirty = [];

		// Get table name from classname, if not set already
		$this->table_name = $this->table_name ?? strtolower(substr(get_class($this), 5));

		// Get table info (columns and primary keys)
		$this->table_info = (new Cache(DB::class))->get($this->table_name, function($table_name)
			{
				$columns = DB::query("SHOW COLUMNS FROM $table_name")->fetchArray(true);
				$columns = array_map('reset', $columns);

				$info = (object)[
					'columns' => $columns,
					'column_names' => array_keys($columns), 
					'primary_keys' => [],
					'rules' => [],
					'auto_increment' => false,
					];

				foreach ($columns as $name => $column)
				{
					// If auto_increment
					if($column['Extra'] == 'auto_increment')
						// Remember name for lastInsertId on save
						$info->auto_increment = $name;

					// If not, and not nullable
					elseif($column['Null'] == 'NO')
						// Add not_empty rule
						$info->rules[$name][] = 'not_empty';

					// Add db_type rule
					$info->rules[$name][] = ['db_type', $column['Type']];
				}

				return $info;
			});
	}

	
	public function __set($key, $value)
	{
		$this->dirty[$key] = $value;
		parent::__set($key, $value);
	}
	
	public function __unset($key)
	{
		$this->dirty[$key] = null;
		parent::__unset($key);
	}



	public function validate()
	{
		$rules = array_merge_recursive($this->table_info->rules, $this->rules);
		Valid::check($this, $rules);
		return $this;
	}



	public function save()
	{
		if( ! $this->dirty)
			return true;

		// Validate
		$this->validate();

		// Make query
		$data = Util::array_whitelist($this->dirty + $this->data, $this->table_info->column_names);
		$columns = array_keys($data);

		$query = sprintf("INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s",
			$this->table_name,
			implode(', ', $columns),
			implode(', ', self::cc($columns)),
			implode(', ', self::cu($columns))
			);

		// Run query
		$x = DB::prepare($query)
			->execute($data);

		// If inserted/updated, get auto_increment value
		if($x->rowCount() > 0 && $this->table_info->auto_increment)
			$this->data[$this->table_info->auto_increment] = $x->lastInsertId();

		// Reset $dirty
		$this->dirty = [];

		return $this;
	}



	// column => column = VALUES(column)
	private static function cu(array $columns)
	{
		return array_map(function($c)
		{
			return "$c = VALUES($c)";
		}, $columns);
	}

	// column => :column
	private static function cc(array $columns)
	{
		return array_map(function($c)
		{
			return ':'.$c;
		}, $columns);
	}
}
