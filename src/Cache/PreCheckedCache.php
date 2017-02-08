<?php

namespace Cache;

/**
 * Preloaded cache.
 */
class PreCheckedCache extends \Cache
{
	protected $loader;

	public function __construct(string $id, callable $loader, ...$cache_validators)
	{
		parent::__construct($id, null, ...$cache_validators);
		$this->loader = $loader;

		// Do a preload if anything is invalid
		if($this->_any_invalid())
			$this->reload();

		// Remove any validators
		$this->valid = [];
	}

	public function reload()
	{
		$x = $this->loader;
		foreach($x() as $key => $value)
			$this->set($key, $value);
	}


	private function _any_invalid(): bool
	{
		$files = glob($this->dir.'*');

		// Empty cache
		if( ! $files)
			return true;

		// Check each file
		foreach($files as $file)
		{
			$mtime = filemtime($file);
			$key = str_replace($this->dir, '', $file);
			if( ! $this->is_valid($mtime, $key))
				return true;
		}
		return false;
	}
}
