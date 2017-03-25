<?php

namespace Cache;
use Log;

/**
 * Checks if given files have changed.
 */
class FileValidator
{
	use \WinPathFix;

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
			{
				$f = self::from_win($f, true);
				Log::trace("$f has changed");
				return false;
			}
		return true;
	}
}
