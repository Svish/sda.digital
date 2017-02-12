<?php

namespace Data;

use Mime;

class FileInfo extends Computed
{
	const HASH_ALGO = 'sha256';

	use \WinPathFix;

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
			=> self::from_win(pathinfo($file, PATHINFO_BASENAME));

		yield 'extension'
			=> self::from_win(pathinfo($file, PATHINFO_EXTENSION));

		yield from Mime::get($file);
		
		yield 'hash'
			=> '$'.self::HASH_ALGO.'$'.hash_file(self::HASH_ALGO, $file);
	}

	public static function verify(string $file, string $hash): bool
	{
		$algo = self::HASH_ALGO;

		if(strpos($hash, '$') === 0)
		{
			$s = strpos($hash, '$', 1);
			$algo = substr($hash, 1, $s-1);
			$hash = substr($hash, $s+1);
		}

		return $hash == hash_file($algo, self::to_win($file));
	}
}
