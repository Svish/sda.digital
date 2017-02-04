<?php

namespace Data;
use DB, Util, Data, Valid;


/**
 * Base class for data from SQL tables.
 */
abstract class Sql extends Data
{
	protected $_table_name;
	protected $_rules;

	private $_table_info;
	private $_dirty;

	public function __construct()
	{
		// Clear dirt from PDO constructor
		$this->_dirty = [];

		// Get table name from classname, if not set already
		$this->_table_name = $this->_table_name 
			?? strtolower(get_class_name($this));

		// Get table info
		$this->_table_info = DB::getTableInfo($this->_table_name);
	}

	
	public function __set($key, $value)
	{
		$this->_dirty[$key] = $value;
		parent::__set($key, $value);
	}
	
	public function __unset($key)
	{
		$this->_dirty[$key] = null;
		parent::__unset($key);
	}



	public function validate()
	{
		$rules = array_merge_recursive($this->_table_info->rules, $this->_rules);
		Valid::check($this, $rules);
		return $this;
	}



	public function save()
	{
		if( ! $this->_dirty)
			return true;

		// Validate
		$this->validate();

		// Make query
		$data = array_whitelist($this->_dirty + $this->data, $this->_table_info->column_names);
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
		if($query->affectedRows() == 1 && $this->_table_info->auto_increment)
			$this->data[$this->_table_info->auto_increment] = $query->lastInsertId();

		// Reset $_dirty
		$this->_dirty = [];

		return $this;
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
}
