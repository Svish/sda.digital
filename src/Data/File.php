<?php

namespace Data;

use DB, ID3;
use File as FileUtils;

class File extends ExtendedSql
{
	const SERIALIZE = true;


	public function __construct()
	{
		parent::__construct();
		$this->computed( new FileInfo('path') );
	}

	public function __isset($key)
	{
		return in_array($key, ['name', 'tag', 'url'])
			|| parent::__isset($key);
	}


	public function __get($key)
	{
		switch($key)
		{
			case 'url':
				return strtolower(get_class_name($this)).'/'.$this->file_id;

			case 'name':
				return $this->filename.$this->extension;

			case 'tag':
				return ID3::instance()
					->read(self::to_win($this->path));

			default:
				return parent::__get($key);
		}
	}

	public function jsonData(): array
	{
		return parent::jsonData() + [
			'url' => $this->url,
			'name' => $this->name,
			'tag' => $this->tag,
			];
	}


	/**
	 * Update path based on hash.
	 *
	 * @return true if path was updated and file moved.
	 */
	public function update_path(): bool
	{
		// Make new file path based on hash and id
		$hash = $this->hash;
		$hash_1 = substr($hash, 0, 2);
		$hash_2 = substr($hash, 2, 2);

		$old = $this->path;
		$new = self::DIR."{$hash_1}\\{$hash_2}\\{$hash}";

		// If not same, move file and update path
		if($old == $new)
			return false;

		$old = self::to_win($old);
		$new = self::to_win($new);

		FileUtils::mkdir(dirname($new));
		if( ! @rename($old, $new))
			throw new Exception("Unable to move file from $old => $new");
		
		$this->toggle();
		$this->path = self::from_win($new, true);
		$this->toggle();
		$this->save();
		return true;
	}

	use \WinPathFix;
}
