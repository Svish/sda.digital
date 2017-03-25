<?php

namespace Model;
use Data, Model, DB, Log;
use Mime, ID3, Valid;
use Data\Content;
use Data\File;
use Data\Person;
use Data\Series;
use Data\ContentPerson;
use Data\SeriesContent;
use Data\FreshLog;


/**
 * Helper model for fresh files.
 */
class Fresh extends \Model
{
	const DIR = ROOT.'_new'.DIRECTORY_SEPARATOR;


	public function for_series(): array
	{
		$uid = Model::users()->logged_in()->user_id;

		return DB::query("SELECT content_id, title, created,
				GROUP_CONCAT(person.name SEPARATOR ', ') 'persons'
				FROM content
					INNER JOIN fresh_log USING (content_id)
					INNER JOIN content_person USING (content_id)
					INNER JOIN person USING (person_id)
				WHERE fresh_log.user_id = 1
				GROUP BY content_id
				ORDER BY content.created")
			->fetchArray();
	}


	/**
	 * Validate and save content data.
	 */
	public function save(array $data)
	{
		Valid::check_array($data, [
			'persons' => ['not_empty'],
		]);

		$uid = Model::users()->logged_in()->user_id;

		try
		{
			DB::begin();

			// Save content to get id
			$content = new Content;
			$content->set($data);
			$content->user_id = $uid;
			$content->save();

			// Add persons
			foreach($data['persons'] as $person)
			{
				$person = Person::from($person);
				$person->save();

				$cp = new ContentPerson;
				$cp->content_id = $content->content_id;
				$cp->person_id = $person->person_id;
				$cp->role = $person->role;
				$cp->save();
			}

			// Add files
			foreach($data['files'] as $f)
			{
				$file = File::from($f);
				$file->content_id = $content->content_id;
				$file->save();
				$files[] = $file;
			}

			// Move files from _new
			foreach($files as $file)
				$file->update_path();

			DB::commit();
		}
		catch(\Exception $e)
		{
			DB::rollback();
			throw $e;
		}

		$log = new FreshLog;
		$log->user_id = $uid;
		$log->content_id = $content->content_id;
		$log->save();
	}




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
		{
			$files[] = [
				'path' => $file->getPathname(),
				'name' => $file->getBasename('.'.$file->getExtension()),
				];
		}

		// Group by name
		$list = array_group_by('name', $files ?? []);

		// Yield content
		foreach($list as $content)
		{
			yield [
				'title' => self::from_win($content['name']),
				'files' => array_map([$this, 'file'], $content['items']),
				'time' => null,
				'summary' => null,
				'persons' => [],
				'location_id' => null,
			];
		}
	}
	private function file($file): File
	{
		$f = new File;
		$f->path = self::from_win($file['path'], true);
		return $f;
	}





	/**
	 * Returns ID3 tag info.
	 */
	public function tag_info(string $path)
	{
		$path = self::safe_path($path);
		$info = ID3::instance()->read($path);
		$info = iterator_to_array($info);

		Log::trace(self::from_win($path, true), $info);

		foreach($info['tags'] as $key => $val)
		switch($key)
		{
			case 'time':
				yield 'time' => $val; break;

			case 'year':
				if( ! array_key_exists('time', $info['tags']))
					yield 'time' => $val;
				break;

			case 'title':
				yield 'title' => $val; break;

			case 'date':
				yield 'time' => $val; break;

			case 'comment':
				yield 'summary' => $val; break;

			case 'artist':
				foreach($val as $i => $artist)
				{
					$person = Model::persons()->find($artist);
					$person->role = $i ? 'translator' : 'speaker';
					$persons[] = $person;
				}

				yield 'persons' => $persons; break;
		}
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
