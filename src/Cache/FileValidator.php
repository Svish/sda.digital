<?php

/**
 * Checks if given files have changed.
 */
class Cache_FileValidator
{
	protected $files;

	/**
	 * @param $files Files to check.
	 */
	public function __construct(array $files)
	{
		$this->files = $files;
	}

	/**
	 * @return FALSE if any given files have changed since $time.
	 */
	public function __invoke($time)
	{
		foreach($this->files as $f)
			if(filemtime($f) > $time)
				return false;
		return true;
	}
}
