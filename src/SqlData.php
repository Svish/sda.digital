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
		// Clear dirt from PDO constructor
		$this->dirty = [];

		// Get table name from classname, if not set already
		$this->table_name = $this->table_name ?? strtolower(substr(get_class($this), 5));

		// Get table info
		$this->table_info = DB::getTableInfo($this->table_name);
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

		// TODO: Use bindValue with PARAM_ according to table_info?

		// Run query
		$x = DB::prepare($query)
			->execute($data);

		// If inserted/updated, get auto_increment value
		if($x->affectedRows() > 0 && $this->table_info->auto_increment)
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
