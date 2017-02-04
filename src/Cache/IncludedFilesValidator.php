<?php

namespace Cache;

/**
 * Checks if any included files have changed.
 */
class IncludedFilesValidator extends FileValidator
{
	public function __construct()
	{
		parent::__construct([]);
	}

	/**
	 * @returns FALSE if any included files have changed since $time.
	 */
	public function __invoke($time)
	{
		$this->files = array_filter(get_included_files(), function($s) 
			{
				return strpos($s, 'vendor'.DIRECTORY_SEPARATOR) === false
					&& strpos($s, '.cache'.DIRECTORY_SEPARATOR) === false;
			});

		return parent::__invoke($time);
	}
}
