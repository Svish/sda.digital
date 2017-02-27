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
	const DIR = ROOT.'_new'.DIRECTORY_SEPARATOR;



	/**
	 * Get list of non-empty directories.
	 */
	public function directories()
	{
		$it = new \RecursiveDirectoryIterator(self::DIR,
			\FilesystemIterator::SKIP_DOTS);

		yield from $this->fresh_dir($it);
	}

	private function fresh_dir(\RecursiveDirectoryIterator $it)
	{
		$file_count = 0;
		while($it->valid())
		{
			if($it->current()->isDir())
				yield from $this->fresh_dir($it->getChildren());
			else
				$file_count++;
			$it->next();
		}

		if( ! $file_count)
			return;

		$path = self::from_win($it->getSubPath());
		yield [
			'title' => str_replace(DIRECTORY_SEPARATOR, ' / ', $path) ?: '$root',
			'path' => $path ?: '.',
			'count' => $file_count,
		];
	}





	/**
	 * Get list of content in $dir.
	 */
	public function content(string $dir)
	{
		$dir = self::DIR.$dir;
		$dir = self::safe_path($dir);

		// Find files
		$it = new \FilesystemIterator($dir);
		$it = new \CallbackFilterIterator($it, function($f)
			{
				return $f->isFile();
			});
		foreach($it as $file)
			$files[] = [
				'path' => $file->getPathname(),
				'name' => $file->getBasename('.'.$file->getExtension()),
				];

		// Group by name
		$list = array_group_by('name', $files ?? []);

		// Yield content
		foreach($list as $content)
		{
			$files = array_map([$this, 'fresh_file'], $content['items']);

			yield \Reflect::pre_construct(Content::class,
				function($new) use ($content, $files)
				{
					$new->title = self::from_win($content['name']);
					$new->file_list = $files;
				});
		}
	}

	private function fresh_file($file): File
	{
		$path = str_replace(ROOT, '', $file['path']);

		$file = new File;
		$file->path = self::from_win($path);
		return $file;
	}





	/**
	 * Analyze file and return whatever we can deduce from it.
	 */
	public function analyze(string $path)
	{
		$path = self::safe_path($path);
		$info = ID3::instance()->read($path);
		return iterator_to_array($info);
	}




	/**
	 * @return to_win( $path )
	 * @throws If $path is outside DIR
	 */
	private static function safe_path(string $path): string
	{
		$path = self::to_win($path);
		$real = realpath($path);

		if(is_dir($real))
			$real .= DIRECTORY_SEPARATOR;

		if( ! starts_with(self::DIR, $real))
			throw new \Error\PleaseNo("Tried to access '$path'");

		return $real;
	}	


	use \WinPathFix;

}
