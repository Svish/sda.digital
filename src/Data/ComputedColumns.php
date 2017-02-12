<?php

namespace Data;

/**
 * Allow columns to be set/unset with .
 */
abstract class ComputedColumns extends Sql
{
	private $_handlers = [];

	/**
	 * Set the handlers to use.
	 */
	protected function computed(Computed ...$computed)
	{
		$this->_handlers = $computed;
	}

	public function __set($key, $value)
	{
		parent::__set($key, $value);

		// If loaded
		if($this->loaded)
			// Check each handler
			foreach($this->_handlers as $x)
				// Set any extra computed columns
				foreach($x->set($key, $value) as $k => $v)
					$this->$k = $v;

	}
	
	public function __unset($key)
	{
		parent::__unset($key);
		
		// Check each handler
		foreach($this->_handlers as $x)
			// Unset any extra computed columns
			foreach($x->unset($key) as $k)
				unset($this->$k);
	}
}
