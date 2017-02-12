<?php

namespace Model;
use \Data\File;
use Mime, ID3;


/**
 * Model for to select, enrich and add new uploaded files.
 */
class Files extends \Model
{
	// @see http://php.net/manual/en/function.hash-file.php
	const HASH_ALGO = 'sha256';

	const DIR_NEW = ROOT.'_new'.DIRECTORY_SEPARATOR;

	use \WinPathFix;


	


	/**
	 * New file object from new file.
	 */
	public function file($path)
	{
		$path = self::to_win($path);

		// Check that file is in allowed directory
		if( ! starts_with(self::DIR_NEW, realpath($path)))
			throw new \Error\PleaseNo("Tried to access '$path'");

		return \Reflect::pre_construct(File::class,
			function($file) use ($path)
			{
				// Populate
				$file->path = self::from_win($path);
				$file->filename = self::pathinfo($path, PATHINFO_BASENAME);
				$file->extension = self::pathinfo($path, PATHINFO_EXTENSION);
				$file->mime = Mime::get($path)['type'];
				$file->sha256 = hash_file(self::HASH_ALGO, $path);
			});
	}


	/**
	 * Get list of selected files to add, enriched with info from file.
	 */
	private function get_file_info($path)
	{
		$info['mime'] = Mime::get($path);

		// NOTE: Works for video too, but seems to be suuuper slow...
		if(starts_with('audio/', $info['mime']['type']))
			$info += ID3::read($path);

		$info += [
			'path' => self::from_win($path),
			'title' => self::pathinfo($path, PATHINFO_FILENAME),
			'filename' => self::pathinfo($path, PATHINFO_FILENAME),
			'fileext' => self::pathinfo($path, PATHINFO_EXTENSION),
			];

		return $info;
	}



	/**
	 * Get list of new files, grouped by directory.
	 */
	public function fresh()
	{
		$it = new \RecursiveDirectoryIterator(self::DIR_NEW,
			\FilesystemIterator::SKIP_DOTS);

		return $this->fresh_group($it);
	}
	private function fresh_group(\RecursiveDirectoryIterator $it)
	{
		$files = [];
		while($it->valid())
		{
			// Recurse down directories
			if($it->current()->isDir())
				yield from $this->fresh_group($it->getChildren());
			// Gather files
			else
				$files[] = $this->fresh_file($it->current());

			$it->next();
		}

		// Ignore group if no files
		if(empty($files))
			return;

		// Sort
		array_sort_by('filename', $files);

		// Yield as group
		yield [
			'path' => self::from_win($it->getSubPath()) ?: '/',
			'files' => $files,
			];
	}
	private function fresh_file(\SplFileInfo $file): File
	{
		$path = str_replace(ROOT, '', $file->getPathname());
		
		return \Reflect::pre_construct(File::class,
			function($file) use ($path)
			{
				$file->path = self::from_win($path);
				$file->filename = self::pathinfo($path, PATHINFO_BASENAME);
			});
	}



	/**
	 * Helper: from_win wrapped pathinfo
	 *
	 * @param $path System path
	 * @param $opts http://php.net/manual/en/function.pathinfo.php
	 * @return Requested part, converted from system path
	 */
	private static function pathinfo(string $path, int $opts): string
	{
		return self::from_win(pathinfo($path, $opts));
	}
}
