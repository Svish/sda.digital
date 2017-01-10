<?php

/**
 * Loads config files.
 */
class Config
{
	const DIR = DOCROOT.'config'.DIRECTORY_SEPARATOR;

	public static $loaded = [];
	public static function __callStatic($name, $args)
	{
		if( ! array_key_exists($name, self::$loaded))
			self::$loaded[$name] = self::_get($name);
		return self::$loaded[$name];
	}


	private static function _get($name)
	{
		$files = glob(self::DIR."{.,}$name.{inc,ini}", GLOB_BRACE)
			or trigger_error("Config for '$name' not found.", E_USER_ERROR);

		return self::_load($files[0]);
	}


	private static function _load($path)
	{
		switch(pathinfo($path, PATHINFO_EXTENSION))
		{
			case 'ini':
				return parse_ini_file($path, true, INI_SCANNER_RAW);
				
			case 'inc':
				return include $path;
		}
	}
}
