<?php

/**
 * Checks if any of the given files are newer than $mtime.
 */
class Cache_FileValidator
{
	private $files;
	public function __construct(array $files)
	{
		$this->files = $files;
	}


	public function __invoke($mtime)
	{
		foreach($this->files as $f)
			if(filemtime($f) > $mtime)
				return false;
		return true;
	}
}
