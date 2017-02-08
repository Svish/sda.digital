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
	 * Preloads cache using callable if cache is empty or *any* keys are invalid.
	 * 
	 * Callable should return/yield $key => $value pairs.
	 */
	public function preload(callable $loader)
	{
		if( ! $this->_all_valid())
			foreach($loader() as $key => $value)
				$this->set($key, $value);

		return $this;
	}

	/**
	 * Reads and unserializes data from the cache file identified by $key.
	 */
	public function get(string $key, $default = NULL)
	{
		$path = $this->path($key);

		// Try get data
		$data = $this->_get($path);

		// Return if existing and valid
        if($data !== NULL && $this->_valid(filemtime($path), $key))
			return unserialize($data);

		// Call and store default if callable
		if(is_callable($default))
		{
			// TODO: Fallback to $data if throws and $data is not empty?
			$default = $default($key);
			return $this->_set($path, $default);
		}

		// Otherwise, return $default
		return $default;
	}
	private function _get(string $path)
	{
		return File::get($path);
	}

	private function _valid(int $mtime, string $key): bool
	{
		// False if any validators fails
		foreach($this->valid as $valid)
			if( ! $valid($mtime, $key))
				return false;

		// Otherwise; valid
		return true;
	}

	private function _all_valid(): bool
	{
		$files = glob($this->dir.'*');

		// Empty cache
		if( ! $files)
			return false;

		// Check each file
		foreach($files as $file)
		{
			$mtime = filemtime($file);
			$key = str_replace($this->dir, '', $file);
			if( ! $this->_valid($mtime, $key))
				return false;
		}
		return true;
	}



	/**
	 * Serializes and stores the $data in a cache file identified by $key.
	 */
	public function set(string $key, $data)
	{
		return $this->_set( $this->path($key) , $data);
	}
	private function _set(string $path, $data)
	{
		if($data instanceof Generator)
			$data = iterator_to_array($data);
		
		File::put($path, serialize($data));
		return $data;
	}


	/**
	 * Return sanitized file path for $key.
	 */
	private function path(string $key): string
	{
		return $this->dir.self::sanitize($key);
	}



	/**
	 * Make the key filename-friendly.
	 */
	private static function sanitize(string $key): string
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
