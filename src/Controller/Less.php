<?php

/**
 * Handles compilation and serving of LESS files as CSS.
 */
class Controller_Less extends CachedController
{
	const DIR = DOCROOT.'src'.DIRECTORY_SEPARATOR.'_less'.DIRECTORY_SEPARATOR;
	const EXT = '.less';

	public function __construct()
	{
		$this->config = self::config();
		$this->config->valid = array_map('basename', glob(self::DIR.'*'.self::EXT));
	}


	public function before(array &$info)
	{
		if( ! in_array($info['params'][2].self::EXT, $this->config->valid))
			HTTP::exit_status(404, $info['path']);

		$this->path = self::DIR.$info['params'][2].self::EXT;
		$this->data = self::compile($this->path);

		parent::before($info);
	}



	public function get($path)
	{
		header('Content-Type: text/css; charset=utf-8');
		$time = date('Y-m-d H:i:s', $this->data['updated']);
		echo "/* $time */ {$this->data['compiled']}";
	}



	protected function cache_valid($cached_time)
	{
		return parent::cache_valid($cached_time)
		   and $cached_time >= $this->data['updated'];
	}


	private static function compile($path)
	{
		$cache = new Cache(__CLASS__);
		$cache_key = basename($path).'c';

		// Get cached if exists
		$old = $cache->get($cache_key, ['root' => $path, 'updated' => 0]);

		// Do a cached compile
		$less = new lessc;
		$less->setFormatter('compressed');
		$new = $less->cachedCompile($old);

		return $new["updated"] > $old["updated"]
			? $cache->set($cache_key, $new)
			: $new;
	}



	public static function config()
	{
		return Config::less();
	}
}
