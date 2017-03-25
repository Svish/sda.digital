<?php

namespace Data;

/**
 * Support "computed" and filtered columns.
 */
abstract class ExtendedSql extends RelationalSql
{
	private $_handlers = [];
	private $on = true;


	public function get_computed_handler(string $type)
	{
		foreach($this->_handlers as $h)
			if($h instanceof $type)
				return $h;
		throw new Exception("$this has no handler of type '$type'");
	}

	/**
	 * Set the handlers to use.
	 */
	protected function computed(Computed ...$handlers)
	{
		$this->_handlers = $handlers;
	}


	/**
	 * To temporarily disable this.
	 */
	protected final function toggle()
	{
		$this->on = ! $this->on;
	}


	public function __set($key, $value)
	{
		parent::__set($key, $value);

		if( ! $this->on || ! $this->loaded)
			return;
		
		foreach($this->_handlers as $handler)
			foreach($handler->set($key, $value) as $k => $v)
			{
				if($k == $key)
					parent::__set($k, $v);
				else
					$this->$k = $v;
			}
	}
	
	
	public function __unset($key)
	{
		parent::__unset($key);
		
		if($this->on)
			foreach($this->_handlers as $handler)
				foreach($handler->unset($key) as $k)
					unset($this->$k);
	}
}
