<?php

namespace Data;
use DB;

abstract class RelationalSql extends ComputedColumns
{
	private $relations;
	private $dirty;

	public function __construct()
	{
		parent::__construct();

		// Get relations
		$this->relations = $this->table_info->relations ?? [];

		// Make sure relation props exists
		foreach($this->relations as $prop => $rel)
			if( ! isset($this->data[$prop]))
				$this->data[$prop] = null;
	}


	public function load(string ...$props): self
	{
		foreach($props as $prop)
		{
			$rel = $this->relations[$prop];
			$keys = array_whitelist($this->data, 
									$this->table_info->primary_keys);

			$this->data[$prop] = DB::prepare($rel['query'])
				->execute($keys)
				->fetchAll($rel['class']);
			unset($this->dirty[$prop]);
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
		$rel = $this->relations[$key] ?? false;

		// If not not loaded, or not a relation
		if( ! $this->loaded || ! $rel)
			return parent::__set($key, $value);
		
		// Depending on relation type
		switch($rel['type'])
		{
			case '1:M':
				// Only set as array
				if( ! is_array($value))
					throw new \Exception("'$key' must be set as array.");

				// Set M key columns to own key
				foreach($value as $obj)
					$obj->{$rel['column']} = $this->{$rel['column']};

				// And relation to M $value
				return parent::__set($key, $value);


			case 'M:1':
				// Set 1 key column to own key
				parent::__set($rel['column'], $value->{$rel['column']});

				// Add 1 to M
				$value->data[$rel['fp']][] = $this;

				// Set relation to 1 $value
				return parent::__set($key, $value);
				

			default:
				throw new \Exception("Support '{$rel['type']}'");
		}

	}

	public function add(string $relation, Sql $object) // Or id?
	{
		$rel = $this->relations[$relation] ?? false;
		
		switch($rel['type'])
		{
			case '1:M':
				var_dump($relation);
				$this->data[$relation][] = $object;
				return;
		}
		
		throw new \Exception("Not an M relation: '$relation'");
	}

	public function remove(string $relation, Sql $object) // Or id?
	{
		throw new \Exception("Not implemented: ".__METHOD__);
		// TODO: Remove from relationship array ($object->_destroy = true)
		// (make sure self gets dirty)
		// Or just quickly remove link in db?
	}

	public function __unset($key)
	{
		$rel = $this->relations[$key] ?? false;

		if( ! $this->loaded || ! $rel)
			return parent::__unset($key);
		
		switch($rel['type'])
		{
			case '1:M':
				// Nullify M key columns
				$value = $this->$key;
				foreach($value as $obj)
					$obj->{$rel['column']} = null;

				// Empty relation
				return parent::__set($key, []);


			case 'M:1':
				// Nullify 1 key column
				parent::__set($rel['column'], null);

				// Nullify relation
				return parent::__set($key, null);


			default:
				throw new \Exception("Support '{$rel['type']}'");
		}
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
			// Begin transaction
			DB::begin();
			
			// Save children
			
			// Save relationships
			// Get current, then reorder / _destroy / etc and insert new?
			// INSERT IGNORE ?
			
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
