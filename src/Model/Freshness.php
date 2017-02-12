<?php

namespace Model;
use Model, Session, Mime, ID3;
use RecursiveDirectoryIterator, FilesystemIterator;


/**
 * Model for to select, enrich and add new uploaded files.
 */
class Freshness extends Model
{
	const DIR = '_new'.DIRECTORY_SEPARATOR;

	use \WinPathFix;



	/**
	 * Get list of selected files to add, enriched with info from file.
	 */
	public function get_selected()
	{
		$list = Session::get('adding', []);
		$list = array_map([$this, 'enrich_selected'], $list);
		$list = array_group_by('filename', $list, false, 'name', 'files');

		return $list;
	}
	private function enrich_selected($path)
	{
		$path = self::to_win($path);

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
	 * Store list of selected files to add.
	 */
	public function store_adding($list)
	{
		Session::set('adding', $list);
	}



	/**
	 * Get list of new files, grouped by directory.
	 */
	public function get_fresh()
	{
		$it = new RecursiveDirectoryIterator(self::DIR,
			FilesystemIterator::SKIP_DOTS);

		return $this->fresh_groups($it);
	}
	private function fresh_groups(RecursiveDirectoryIterator $it)
	{
		// Gather files
		$files = [];
		while($it->valid())
		{
			// Recurse down directories
			if($it->current()->isDir())
				yield from $this->fresh_groups($it->getChildren());
			else
				$files[] = $this->fresh_file($it->current());

			$it->next();
		}

		// Ignore if no files
		if(empty($files))
			return;

		// Sort
		array_sort_by('name', $files);
		$files = array_group_by('name', $files, false, 'name', 'files');

		// Yield as group
		yield [
			'directory' => self::from_win($it->getSubPath()) ?: '/',
			'content' => $files,
			];
	}
	private function fresh_file($file)
	{
		return
		[
			'name' => self::pathinfo($file->getPathname(), PATHINFO_FILENAME),
			'path' => self::from_win($file->getPathname()),
		];
	}



	/**
	 * Helper: from_win wrapped pathinfo
	 */
	private static function pathinfo($path, $options)
	{
		return self::from_win(pathinfo($path, $options));
	}
}
