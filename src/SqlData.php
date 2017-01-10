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
				$columns = DB::query("SHOW COLUMNS FROM $table_name")->fetchArray();
				$info = (object)['columns' => [], 'primary_keys' => []];
				foreach ($columns as $c)
				{
					$info->columns[] = $c['Field'];
					if($c['Key'] == 'PRI')
						$info->primary_keys[] = $c['Field'];
				}

				return $info;
			});

		// TODO: Add rules from table info? (max_length, unique, not_empty, ...)
	}

	
	public function __set($key, $value)
	{
		if($this->table_info && in_array($key, $this->table_info->primary_keys))
			throw new Exception("Primary key '$key' cannot be changed.");

		$this->dirty[$key] = $value;
		parent::__set($key, $value);
	}
	
	public function __unset($key)
	{
		if($this->table_info && in_array($key, $this->table_info->primary_keys))
			throw new Exception("Primary key '$key' cannot be changed.");

		$this->dirty[$key] = null;
		parent::__unset($key);
	}


	public function save()
	{
		if( ! $this->dirty)
			return true;

		// Validate
		Valid::check($this, $this->rules);

		// Make query
		$data = Util::array_whitelist($this->dirty + $this->data, $this->table_info->columns);
		$columns = array_keys($data);

		$query = sprintf("INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s",
			$this->table_name,
			implode(', ', $columns),
			implode(', ', self::cc($columns)),
			implode(', ', self::cu($columns))
			);

		// Run query
		$c = DB::prepare($query)
			->execute($data)
			->rowCount();

		$this->dirty = [];

		return $c;
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
