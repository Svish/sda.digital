<?php

/**
 * Base class for data objects.
 */
class Data implements JsonSerializable
{
	protected $data = [];

	public function __construct(array $data = null)
	{
		if($data)
			$this->set($data);
	}



	public function set(array $data): self
	{
		foreach($data as $k => $v)
			$this->{$k} = $v;
		
		return $this;
	}



	public function __get($key)
	{
		return $this->data[$key] ?? null;
	}

	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}	

	public function __isset($key)
	{
		return $this->{$key} !== null;
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
		if($keys === false || $keys == [])
			return [];

		// Get json data
		$data = $this->jsonData();

		// All
		if($keys === true)
			return $data;

		// Only whitelisted
		return array_whitelist($data, static::SERIALIZE);
	}

	public function jsonData(): array
	{
		return ['__type' => get_class($this)]
			+ $this->data;
	}

	public static function from($data)
	{
		$class = array_remove($data, '__type');

		return Reflect::pre_construct($class ?? static::class,
			function ($obj) use ($data)
			{
				$obj->data = $data;
			});
	}
}
