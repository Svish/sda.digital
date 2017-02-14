<?php

namespace Data;
use DB;

abstract class RelationalSql extends ComputedColumns
{
	private $relations;

	public function __construct()
	{
		parent::__construct();

		$this->relations = $this->table_info->relations ?? [];

		foreach($this->relations as $prop => $rel)
			if( ! isset($this->data[$prop]))
				$this->data[$prop] = null;
	}


	public function load(string ...$props)
	{
		foreach($props as $prop)
		{
			$rel = $this->relations[$prop];
			$keys = array_whitelist($this->data, 
									$this->table_info->primary_keys);

			$this->data[$prop] = DB::prepare($rel['query'])
				->execute($keys)
				->fetchAll($rel['class']);		
		}
		return $this;
	}


	public function __get($key)
	{
		// Load first if null and a relation
		if($this->loaded
		&& parent::__get($key) === null
		&& array_key_exists($key, $this->relations))
		{
			$this->load($key);
		}

		return parent::__get($key);
	}
	
	public function __set($key, $value)
	{
		if($this->loaded)
		{
			$rel = $this->relations[$key] ?? false;
			if($rel)
			{
				parent::__set($rel['column'], $value[$rel['column']]);
				parent::__set($key, $value);
				return;
			}
		}

		parent::__set($key, $value);
	}

	public function __unset($key)
	{
		if($this->loaded)
		{
			$rel = $this->relations[$key] ?? false;
			if($rel)
			{
				parent::__set($key, null);
				parent::__unset($rel['column']);
				return;
			}
		}

		parent::__unset($key);
	}

	public function add(string $relationship, Sql $object) // Or id?
	{
		// TODO: Add to relationship array
		// (make sure self gets dirty)
	}

	public function remove(string $relationship, Sql $object) // Or id?
	{
		// TODO: Remove from relationship array ($object->_destroy = true)
		// (make sure self gets dirty)
	}

	public function is_dirty()
	{
		if(parent::is_dirty())
			return true;

		// TODO: Check relationships, if any
		// Use _destroy to remove?
		// Set flag in Query->fetch to check if object is from DB?
		return false;
	}



	public function save()
	{
		$saved = false;
		
		try
		{
			DB::begin();
			// Begin transaction
			
			// Save children

			// Save relationships

			// Save self
			$saved = $saved || parent::save();
			
			// End transaction
			DB::commit();	
		}
		catch(\PDOException $e)
		{
			DB::rollback();
			throw $e;
		}

		return $saved;
	}
}
