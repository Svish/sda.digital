<?php

namespace Model;
use Mime, ID3;
use \Data\File;
use \Data\Content;
use \Data\Series;


/**
 * Model for fresh files.
 */
class Fresh extends \Model
{
	const DIR_NEW = ROOT.'_new'.DIRECTORY_SEPARATOR;
	

	/**
	 * Analyze file.
	 */
	public function analyze(string $path): File
	{
		$path = self::to_win($path);
		
		// Check that file is in allowed directory
		if( ! starts_with(self::DIR_NEW, realpath($path)))
			throw new \Error\PleaseNo("Tried to access '$path'");

		$file = new File;
		$file->path = self::from_win($path);

		if(starts_with('audio/', $file->mime))
			$file->id3 = ID3::read($path);

		return $file;
	}



	/**
	 * Get list of new stuff.
	 */
	public function list()
	{
		$it = new \RecursiveDirectoryIterator(self::DIR_NEW,
			\FilesystemIterator::SKIP_DOTS);

		yield from $this->fresh_series($it);
	}
	private function fresh_series(\RecursiveDirectoryIterator $it)
	{
		$content = [];
		while($it->valid())
		{
			// Recurse down directories
			if($it->current()->isDir())
				yield from $this->fresh_series($it->getChildren());
			// Gather files
			else
				$content[] = $this->fresh_content($it->current());

			$it->next();
		}

		// Ignore group if no content
		if(empty($content))
			return;

		// Sort
		//array_sort_by('filename', $content);

		// Yield as series
		yield \Reflect::pre_construct(Series::class,
			function($series) use ($content, $it)
			{
				$series->title = self::from_win($it->getSubPath()) ?: '/';
				$series->content = $content;
			});
	}
	private function fresh_content(\SplFileInfo $file): Content
	{
		return \Reflect::pre_construct(Content::class,
			function($content) use ($file)
			{
				$content->title = self::from_win($file->getFilename());
				$content->files = [self::fresh_file($file)];
			});
	}
	private function fresh_file(\SplFileInfo $file): File
	{
		$path = str_replace(ROOT, '', $file->getPathname());
		$file = new File;
		$file->path = self::from_win($path);
		return $file;
	}

	use \WinPathFix;

}
