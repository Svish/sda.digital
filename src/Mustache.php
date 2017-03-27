<?php

/**
 * Mustache_Engine wrapper with some defaults and other stuff.
 */
class Mustache extends Mustache_Engine
{
	const DIR = SRC.'_views'.DIRECTORY_SEPARATOR;

	public static function engine(array $options = [], $template = null)
	{
		if($template && is_dir(self::DIR.$template))
			$options += ['partials_loader'
				=> new Mustache_Loader_CascadingLoader([
					new Mustache\FilesystemLoader(self::DIR.$template),
					new Mustache\FilesystemLoader(self::DIR),
				])];

		return new self($options + [
			'cache' => Cache::DIR . __CLASS__,
			'pragmas' => [Mustache_Engine::PRAGMA_FILTERS],
			'strict_callables' => true,
			'logger' => new Mustache\NoWarningsPls,
			'loader' => 
				new Mustache\FilesystemLoader(self::DIR),
			]);
	}
}
