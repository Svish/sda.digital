<?php

class Mustache
{
	const DIR = __DIR__.DIRECTORY_SEPARATOR.'_views'.DIRECTORY_SEPARATOR;

	public static function engine($loader = null)
	{
		return new Mustache_Engine([
			'cache' => Cache::DIR . __CLASS__,
			'loader' => $loader ?? new Mustache_Loader_FilesystemLoader(self::DIR),
			'pragmas' => [Mustache_Engine::PRAGMA_FILTERS],
			]);
	}
}