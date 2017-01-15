<?php

define('IS_WIN', strpos(strtolower(PHP_OS), 'win') === 0);


/**
 * User model for handling logins, etc.
 */
class Model_NewFiles extends Model
{
	const DIR = '_new'.DIRECTORY_SEPARATOR;

	/**
	 * Get user by id or email.
	 */
	public function all()
	{
		$it = new RecursiveDirectoryIterator(self::DIR, FilesystemIterator::SKIP_DOTS);

		return $this->group($it);
	}


	private function group(RecursiveDirectoryIterator $it)
	{
		$files = [];
		$groups = [];

		while($it->valid())
		{
			if($it->current()->isDir())
				$groups[] = $this->group($it->getChildren());
			else
				$files[] = self::file_info($it->current());

			$it->next();
		}

		return self::group_info($it) +
		[
			'groups' => $groups,
			'files' => $files,
			'hasFiles' => !empty($files),
		];

 		foreach($it as $path => $item)
 		{
			if(IS_WIN)
				$path = utf8_encode($path);
 		}
	}

	private function group_info($it)
	{
		return
		[
			'path' => DIRECTORY_SEPARATOR.$this->pathfix($it->getSubPath()) ?: DIRECTORY_SEPARATOR,
			'name' => $this->pathfix(basename($it->getSubPath())),
		];
	}

	private function file_info($file)
	{
		return 
		[
			'path' => self::pathfix($file->getPathname()),
			'size' => $file->getSize(),
			'sizeh' => File::human_size($file->getSize()),
		] + array_map([__CLASS__, 'pathfix'], pathinfo($file->getPathname()));
	}



	private static function pathfix($path)
	{
		return IS_WIN ? utf8_encode($path) : $path;
	}


	/*
var_dump(stat(utf8_decode('X:\dev\www\sda.digital\_new\I Naturens Tempel\02 Salige er de som hungrer og tørster etter rettferdighet.mp3')));
	*/

}
