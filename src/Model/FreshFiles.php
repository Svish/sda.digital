<?php

namespace Model;
use Model;
use RecursiveDirectoryIterator,FilesystemIterator;


/**
 * User model for handling logins, etc.
 */
class FreshFiles extends Model
{
	use \WinPathFix;

	const DIR = '_new'.DIRECTORY_SEPARATOR;

	/**
	 * Returns a structure of grouped, new files.
	 */
	public function find()
	{
		$it = new RecursiveDirectoryIterator(self::DIR, FilesystemIterator::SKIP_DOTS);

		return $this->group($it);
	}


	private function group(RecursiveDirectoryIterator $it)
	{
		// Gather files and yield sub-groups
		$files = [];
		while($it->valid())
		{
			if($it->current()->isDir())
				yield from $this->group($it->getChildren());
			else
				$files[] = self::file_info($it->current());

			$it->next();
		}

		// Sort
		if(empty($files))
			return;

		yield [
			'group' => self::from_win(basename($it->getSubPath())) ?: '/',
			'files' => $files,
			];
	}

	private function file_info($file)
	{
		return
		[
			'name' => self::from_win(pathinfo($file->getPathname(), PATHINFO_BASENAME)),
			'path' => self::from_win($file->getPathname()),
		];
	}
}
