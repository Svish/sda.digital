<?php

/**
 * Base class for data objects.
 */
abstract class Data implements ArrayAccess, JsonSerializable
{
	/**
	 * Returns a new Data_$name.
	 */
	public static function __callStatic($name, $args)
	{
		$name = __CLASS__.'\\'.ucfirst($name);
		return new $name(...$args);
	}



	protected $data = [];


	public function __get($key)
	{
		return $this->data[$key] ?? null;
	}

	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function set(array $data)
	{
		foreach($data as $k => $v)
			$this->$k = $v;
		
		return $this;
	}
	

	public function __isset($key)
	{
		return array_key_exists($key, $this->data);
	}
	
	public function __unset($key)
	{
		unset($this->data[$key]);
	}



	public function __call($method, $args)
	{
		if(is_callable($this->data[$method]))
			return call_user_func_array($this->data[$method], $args);
	}



	/** ArrayAccess implementation **/
	public function offsetSet($offset, $value)
	{
		$this->$offset = $value;
	}
	public function offsetExists($offset)
	{
		return isset($this->$offset);
	}
	public function offsetUnset($offset)
	{
		unset($this->$offset);
	}
	public function offsetGet($offset)
	{
		return $this->$offset;
	}


	/**
	 * JSON serialization.
	 */
	const SERIALIZE = false;
	public function jsonSerialize()
	{
		$keys = static::SERIALIZE;

		// Sanity check...
		if( ! is_bool($keys) && ! is_array($keys))
			throw new \Exception(get_class($this).'::SERIALIZE must be array of keys to serialize, or boolean (true=all, false=none)');
		
		// None (default)
		if($keys === false)
			return [];

		// All
		if($keys === true || $keys == [])
			return $this->data;

		// Only whitelisted
		return array_whitelist($this->data, static::SERIALIZE);
	}
}
