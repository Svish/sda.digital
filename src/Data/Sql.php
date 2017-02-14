<?php

namespace Data;
use DB, Valid, Security;


/**
 * Base class for data from SQL tables.
 */
abstract class Sql extends \Data
{
	const TABLE_NAME = null;
	const RESTRICTED = [];

	protected $table_name;
	protected $table_info;

	protected $rules = [];
	
	protected $loaded = false;
	private $dirty = [];

	public function __construct()
	{
		// Get table name
		$this->table_name = $this->table_name();

		// Get table info
		$this->table_info = DB::table_info($this->table_name);

		// Make sure columns exists in $data for serialization
		foreach($this->table_info->column_names as $col)
			if( ! isset($this->data[$col]))
				$this->data[$col] = null;

		// Done loading from PDO or wherever
		$this->loaded = true;
	}


	
	public function __set($key, $value)
	{
		if($this->loaded)
		{
			// Check if restricted property
			$roles = static::RESTRICTED[$key] ?? [];
			if($roles)
				Security::require($roles);

			// Clean set
			$column = $this->table_info->columns[$key] ?? false;
			if($column && strpos($column['db_type'], 'set(') === 0)
				$value = preg_replace('/\s+/', '', $value);

			// Add to dirty if different
			if($column && $this->data[$key] ?? null != $value)
				$this->dirty[$key] = $value;
		}
		
		parent::__set($key, $value);
	}
	
	public function __unset($key)
	{
		// "Remove" by setting dirty value to null
		if(isset($this->$key))
			$this->dirty[$key] = null;

		// Only actually unset non-column properties
		if(in_array($key, $this->table_info->column_names))
			parent::__set($key, null);
		else
			parent::__unset($key);
	}




	/**
	 * Gets and optionally sets the primary keys.
	 *
	 * @return [$pk1, ...] or false if null
	 */
	public function pk(...$keys)
	{
		if(count($this->table_info->primary_keys) == count($keys))
			foreach($this->table_info->primary_keys as $k => $key)
				$this->{$this->table_info->primary_keys[$k]} = $keys[$k];

		$keys = array_whitelist($this->data, $this->table_info->primary_keys);
		return array_filter($keys) ?: false;
	}


	/**
	 * Gets and optionally sets the primary keys.
	 *
	 * @return $this
	 * @see Valid::check
	 */
	public function validate(): self
	{
		$rules = array_merge_recursive($this->rules, $this->table_info->rules);
		Valid::check($this, $rules);
		return $this;
	}


	/**
	 * @return true if any table columns have new values; otherwise false.
	 */
	public function is_dirty()
	{
		return ! empty($this->dirty);
	}



	/**
	 * @return true if saved; false if no column changes, i.e. not saved
	 */
	public function save()
	{
		// Check if dirty
		if( ! self::is_dirty())
			return false;

		// Validate
		$this->validate();

		// Get data
		$data = array_whitelist($this->dirty + $this->data,
								$this->table_info->column_names);

		// Make query
		$column_names = array_keys($data);
		$query = sprintf("INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s",
			$this->table_name,
			implode(', ', $column_names),
			implode(', ', self::cc($column_names)),
			implode(', ', self::cu($column_names))
			);

		// Prepare
		$query = DB::prepare($query);
		foreach($data as $column => $value)
			$query->bindValue($column, $value, $this->table_info->columns[$column]['pdo_type']);

		// Run query
		$affected_rows = $query->exec($data);


		// If insert
		if($affected_rows == 1)
			// Look for auto_increment column
			foreach($this->table_info->columns as $key => $col)
				// If found, get id
				if($col['auto_increment'])
					$this->data[$key] = $query->lastInsertId();

		// Reset dirt
		$this->dirty = [];

		return true;
	}



	/**
	 * "column" => "column = VALUES(column)"
	 */
	private static function cu(array $columns): array
	{
		return array_map(function($c)
		{
			return "$c = VALUES($c)";
		}, $columns);
	}

	/**
	 * "column" => ":column"
	 */
	private static function cc(array $columns): array
	{
		return array_map(function($c)
		{
			return ":$c";
		}, $columns);
	}



	/**
	 * Get table name from class name.
	 */
	public static final function table_name(): string
	{
		if(static::TABLE_NAME)
			return static::TABLE_NAME;

		$name = get_class_name(static::class);
		$name = preg_replace('/(?<=[[:lower:]])([[:upper:]])/', '_$1', $name);
		return strtolower($name);
	}





	public static function get(...$keys)
	{
		// Return new empty if no keys
		if( ! array_filter($keys))
			return new static;

		$query = self::get_query(__FUNCTION__, $keys);
		
		$obj = DB::prepare($query)
			->execute($keys)
			->fetchFirst(static::class);

		if( ! $obj)
			throw new \Error\NotFound(implode(', ', $keys), static::class);
		else
			return $obj;
	}

	public static function delete(...$keys)
	{
		$query = self::get_query(__FUNCTION__, $keys);
		
		return DB::prepare($query)
			->exec($keys) > 0;
	}

	private static function get_query(string $name, array $keys): string
	{
		$info = DB::table_info(self::table_name());

		if(count($keys) != count($info->primary_keys))
			throw new \Exception('Incorrect number of keys');

		return $info->query[$name];
	}
}
