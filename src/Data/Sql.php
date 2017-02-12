<?php

namespace Data;
use DB, Data, Valid, Security;


/**
 * Base class for data from SQL tables.
 */
abstract class Sql extends Data
{
	const RESTRICTED = [];

	protected $rules;
	
	protected $loaded;

	private $_table_name;
	private $_table_info;
	private $_dirty;

	public function __construct()
	{
		// Get table name
		$this->_table_name = $this->get_table_name();

		// Get table info
		$this->_table_info = DB::getTableInfo($this->_table_name);

		// Make sure columns exists in $data for serialization
		foreach($this->_table_info->column_names as $column)
			if( ! isset($this->$column))
				$this->$column = null;

		// Clean dirt and done loading from PDO or wherever
		$this->_dirty = [];
		$this->loaded = true;
	}


	
	public function __set($key, $value)
	{
		// Check if restricted property
		$roles = static::RESTRICTED[$key] ?? [];
		if($this->loaded && $roles)
			Security::require($roles);

		// Add to dirty if different
		if($this->$key != $value)
			$this->_dirty[$key] = $value;

		parent::__set($key, $value);
	}
	
	public function __unset($key)
	{
		// "Remove" by setting dirty value to null
		if(isset($this->$key))
			$this->_dirty[$key] = null;

		parent::__unset($key);
	}



	public function validate()
	{
		$rules = array_merge_recursive($this->_table_info->rules, 
										$this->rules ?? []);
		Valid::check($this, $rules);
		return $this;
	}


	/**
	 * @return false if no changes; otherwise true
	 */
	public function save()
	{
		if( ! $this->_dirty)
			return false;

		// Validate
		$this->validate();

		// Make query
		$data = array_whitelist($this->_dirty + $this->data,
								$this->_table_info->column_names);
		$column_names = array_keys($data);

		$query = sprintf("INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s",
			$this->_table_name,
			implode(', ', $column_names),
			implode(', ', self::cc($column_names)),
			implode(', ', self::cu($column_names))
			);

		// Prepare
		$query = DB::prepare($query);
		foreach($data as $column => $value)
			$query->bindValue($column, $value, $this->_table_info->column_pdo_types[$column]);

		// Run query
		$query->execute($data);

		// If inserted, get auto_increment value
		if($query->affectedRows() == 1 && $this->_table_info->auto_inc_column)
			$this->data[$this->_table_info->auto_inc_column] = $query->lastInsertId();

		// Reset $_dirty
		$this->_dirty = [];

		return true;
	}



	/**
	 * "column" => "column = VALUES(column)"
	 */
	private static function cu(array $columns)
	{
		return array_map(function($c)
		{
			return "$c = VALUES($c)";
		}, $columns);
	}

	/**
	 * "column" => ":column"
	 */
	private static function cc(array $columns)
	{
		return array_map(function($c)
		{
			return ":$c";
		}, $columns);
	}

	/**
	 * Get table name from class name.
	 */
	protected function get_table_name()
	{
		$name = get_class_name($this);
		$name = preg_replace('/(?<=[[:lower:]])([[:upper:]])/', '_$1', $name);
		return strtolower($name);
	}
}
