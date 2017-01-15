<?php

/**
 * Mustache_Engine wrapper with some defaults and other stuff.
 */
class Mustache extends Mustache_Engine
{
	const DIR = __DIR__.DIRECTORY_SEPARATOR.'_views'.DIRECTORY_SEPARATOR;

	public static function engine(array $options = [], $template = null)
	{
		if($template && is_dir(self::DIR.$template))
			$options += ['partials_loader'
				=> new Mustache_Loader_CascadingLoader([
					new Mustache_Loader_FilesystemLoader(self::DIR.$template),
					new Mustache_Loader_FilesystemLoader(self::DIR),
				])];

		return new self($options + [
			'cache' => Cache::DIR . __CLASS__,
			'pragmas' => [Mustache_Engine::PRAGMA_FILTERS],
			'strict_callables' => true,
			'logger' => new NoWarningsPls,
			'loader' => 
				new Mustache_Loader_FilesystemLoader(self::DIR),
			]);
	}
}

class NoWarningsPls extends Mustache_Logger_AbstractLogger
{
	public function log($level, $message, array $context = [])
	{
		if($level == Mustache_Logger::WARNING)
		{
			foreach($context as $key => $val)
			{
				$context['{'.$key.'}'] = $val;
				unset($context[$key]);
			}
			$message = strtr($message, $context);
			throw new Exception($message);
		}
	}
}
