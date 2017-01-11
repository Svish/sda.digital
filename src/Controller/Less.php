<?php

/**
 * Handles compilation and serving of less files as css.
 */
class Controller_Less extends CachedController
{
	public function __construct()
	{
		$this->config = self::config();
	}


	public function before(array &$info)
	{
		// Check if known path
		if( ! array_key_exists($info['path'], $this->config->path))
			HTTP::exit_status(404, $info['path']);

		$this->path = $this->config->path[$info['path']];
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
