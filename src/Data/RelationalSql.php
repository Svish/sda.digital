<?php

namespace Data;
use DB;

abstract class RelationalSql extends Sql
{
	private $relations;
	private $dirty;

	public function __construct()
	{
		parent::__construct();

		// Get relations
		$this->relations = $this->table_info->relations ?? [];
	}


	public function load_relations(string ...$props): self
	{
		if(empty($props))
			$props = array_keys($this->relations);

		foreach($props as $prop)
		{
			$rel = $this->relations[$prop];

			$key = [
				$rel['column'] => $this->{$rel['column']},
				];

			$query = DB::prepare($rel['query'])
				->execute($key);

			$this->$prop = ends_with('_list', $prop)
				? $query->fetchAll($rel['class'])
				: $query->fetchFirst($rel['class']);

			unset($this->dirty[$prop]);
		}
		return $this;
	}


	public function __get($key)
	{
		// TODO: Load if empty relationship? $this->loaded[$rel] ?
		return parent::__get($key);
	}
	
	public function __set($key, $value)
	{
		$rel = $this->relations[$key] ?? false;

		// Only proceed if loaded, relationship and not null
		if( ! $this->loaded || ! $rel || is_null($value))
			return parent::__set($key, $value);

		// Depending on relation type
		switch($rel['type'])
		{
			case 'M:M':
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
				//parent::__set($rel['column'], $value->{$rel['column']});

				// Add 1 to M
				//$value->data[$rel['fp']][] = $this;

				// Set relation to 1 $value
				return parent::__set($key, $value);
				

			default:
				var_dump(get_defined_vars());
				throw new \Exception("Support '{$rel['type']}'");
		}

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
		// Check "not-empty" relationships
		return false;
	}



	public function save(bool $force = false): bool
	{
		return parent::save($force);

		$saved = false;
		
		try
		{
			// Begin transaction
			DB::begin();
			
			// Save children
			
			// TODO: Save relationships
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


	public function jsonData(): array
	{
		$columns = array_keys($this->table_info->relations);
		
		return parent::jsonData()
			+ array_fill_keys($columns, null);
	}
}
