<?php

namespace Data;

use Mime;

class FileInfo extends Computed
{
	use \WinPathFix;
	
	const HASH_ALGO = 'sha256';


	protected function _unset(string $key)
	{
		yield 'filename';
		yield 'extension';
		yield 'hash';
		yield 'type';
		yield 'encoding';
		yield 'description';
	}

	protected function _set(string $key, $file)
	{
		$file = self::to_win($file);
		
		yield 'filename'
			=> self::from_win(pathinfo($file, PATHINFO_FILENAME));

		yield 'extension'
			=> self::from_win('.'.pathinfo($file, PATHINFO_EXTENSION));
		
		yield 'hash'
			 => hash_file(self::HASH_ALGO, $file);

		yield from Mime::get($file);
	}


	public static function verify_hash(string $file, string $hash): bool
	{
		return $hash == hash_file(self::HASH_ALGO, self::to_win($file));
	}
}
