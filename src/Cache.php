<?php

/**
 * Cache helper.
 */
class Cache
{	
	const DIR = ROOT.'.cache'.DIRECTORY_SEPARATOR;
	
	protected $dir;
	protected $valid = [];


	/**
	 * Creates a new cache instance.
	 *
	 * @param id Identifier for this cache
	 * @param cache_validators 
	 *			TTL seconds => int
	 *			Files to check modified => array
	 *			Custom => callable(int $mtime, string $key): bool
	 */
	public function __construct(string $id, ...$cache_validators)
	{
		// Set cache directory
		$this->dir = self::DIR.$id.DIRECTORY_SEPARATOR;

		// Unless first is null, add default file validator
		if(null !== reset($cache_validators))
			$this->valid[] = new Cache\IncludedFilesValidator();

		// Add cache validators
		foreach($cache_validators as $v)
		{
			// int: TTL
			if(is_int($v))
				$this->valid[] = new Cache\TimeValidator($v);

			// array: list of files to check
			elseif(is_array($v))
				$this->valid[] = new Cache\FileValidator($v);

			// callable: callable to call
			elseif(is_callable($v))
				$this->valid[] = $v;
		}
	}

	/**
	 * Reads and unserializes data from the cache file identified by $key.
	 */
	public function get(string $key, $default = NULL)
	{
		$file = $this->path($key);

		// Try get data
		$data = $this->_get($file);

		// Return if existing and valid
        if($data !== NULL && $this->is_valid(filemtime($file), $key))
			return unserialize($data);

		// Call and store default if callable
		if(is_callable($default))
		{
			// TODO: Fallback to $data if throws and $data is not empty?
			$default = $default($key);
			return $this->_set($file, $default);
		}

		// Otherwise, return $default
		return $default;
	}
	private function _get(string $file)
	{
		return File::get($file);
	}

	protected function is_valid(int $mtime, string $key): bool
	{
		// False if any validators fails
		foreach($this->valid as $valid)
			if( ! $valid($mtime, $key))
				return false;

		// Otherwise; valid
		return true;
	}



	/**
	 * Serializes and stores the $data in a cache file identified by $key.
	 */
	public function set(string $key, $data)
	{
		return $this->_set($this->path($key), $data);
	}
	private function _set(string $file, $data)
	{
		if($data instanceof Generator)
			$data = iterator_to_array($data);
		
		File::put($file, serialize($data));
		return $data;
	}


	/**
	 * Return sanitized file path for $key.
	 */
	protected function path(string $key): string
	{
		return $this->dir.self::sanitize($key);
	}



	/**
	 * Make the key filename-friendly.
	 */
	protected static function sanitize(string $key): string
	{
		return preg_replace('/[^.a-z0-9_-]+/i', '-', $key);
	}



	/**
	 * Delete the cache for this $id.
	 */
	public function clear()
	{
		File::rdelete($this->dir);
	}
	

	/**
	 * Delete the whole cache.
	 */
	public static function clear_all()
	{
		File::rdelete(self::DIR);
	}
}
