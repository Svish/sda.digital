<?php

/**
 * Cache helper.
 */
class Cache
{	
	const DIR = DOCROOT.'.cache'.DIRECTORY_SEPARATOR;
	
	private $dir;
	protected $valid = [];

	use MemberLambdaFix;

	/**
	 * Creates a new cache instance.
	 *
	 * @param id Identifier for this cache
	 * @param cache_validators Int for TTL seconds; Callable($mtime, $key) for custom; otherwise none
	 */
	public function __construct($id, ...$cache_validators)
	{
		// Set cache directory
		$this->dir = self::DIR.$id.DIRECTORY_SEPARATOR;

		// Add cache validators
		foreach($cache_validators as $v)
		{
			// int: TTL
			if(is_int($v))
				$this->valid[] = new Cache_TimeValidator($v);

			// array: list of files to check
			if(is_array($v))
				$this->valid[] = new Cache_FileValidator($v);

			// callable: callable to call
			elseif(is_callable($v))
				$this->valid[] = $v;
		}
	}



	/**
	 * Reads and unserializes data from the cache file identified by $key.
	 */
	public function get($key, $default = NULL)
	{
		$path = $this->path($key);

		// Try get data
		$data = $this->_get($path);

        if($data !== NULL && $this->_valid(filemtime($path), $key))
			return unserialize($data);

		// Call and store default if callable
		if(is_callable($default))
		{
			// TODO: Fallback to $data if throws and $data is not empty?
			$default = $default($key);
			return $this->_set($path, $default);
		}

		// Otherwise, just return default
		return $default;
	}
	private function _get($path)
	{
		return File::get($path);
	}

	private static $checked_files = [];
	private function _valid($mtime, $key)
	{
		if(isset($_GET['no-cache']))
			return false;

		// First check included files (except vendor and .cache files)
		$lmod = array_filter(get_included_files(), function($s) 
			{
				return strpos($s, 'vendor'.DIRECTORY_SEPARATOR) === false
					&& strpos($s, '.cache'.DIRECTORY_SEPARATOR) === false;
			});
		$lmod = array_diff($lmod, self::$checked_files);
		self::$checked_files = array_merge(self::$checked_files, $lmod);
		$lmod = array_map('filemtime', $lmod);
		$lmod = array_reduce($lmod, 'max');

		// If any unchecked files have changed
		if($lmod !== null && $mtime < $lmod)
			return false;

		// If any validators fails
		foreach($this->valid as $valid)
			if( ! $valid($mtime, $key))
				return false;

		// Otherwise; valid
		return true;
	}



	/**
	 * Serializes and stores the $data in a cache file identified by $key.
	 */
	public function set($key, $data)
	{
		return $this->_set( $this->path($key) , $data);
	}
	private function _set($path, $data)
	{
		if($data instanceof Generator)
			$data = iterator_to_array($data);
		
		File::put($path, serialize($data));
		return $data;
	}


	/**
	 * Return sanitized file path for $key.
	 */
	private function path($key)
	{
		return $this->dir.self::sanitize($key);
	}



	/**
	 * Make the key filename-friendly.
	 */
	private static function sanitize($key)
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
