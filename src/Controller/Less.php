<?php

namespace Controller;
use Config, Cache;
use lessc;

/**
 * Handles compilation and serving of LESS files as CSS.
 */
class Less extends Cached
{
	const DIR = SRC.'_less'.DIRECTORY_SEPARATOR;
	const EXT = '.less';

	private $config;
	private $file;

	public function __construct()
	{
		parent::__construct();

		$this->config = Config::less();
		$this->config->valid = array_map('basename', glob(self::DIR.'*'.self::EXT));
	}


	public function before(array &$info)
	{
		$file = $info['params'][2].self::EXT;
		if( ! in_array($file, $this->config->valid))
			throw new \Error\PageNotFound();

		$this->file = self::DIR.$file;
		$this->data = self::compile($this->file);

		parent::before($info);
	}



	public function get()
	{
		header('Content-Type: text/css; charset=utf-8');
		$time = date('Y-m-d H:i:s', $this->data['updated']);
		echo "/* Compiled: $time */\r\n{$this->data['compiled']}";
	}



	protected function cache_valid($cached_time)
	{
		return parent::cache_valid($cached_time)
		   and $cached_time >= $this->data['updated'];
	}


	private static function compile($file)
	{
		$cache = new Cache(__CLASS__);
		$cache_key = basename($file).'c';

		// Get cached if exists
		$old = $cache->get($cache_key, ['root' => $file, 'updated' => 0]);

		// Do a cached compile
		$less = new lessc;
		$less->setFormatter('compressed');
		$new = $less->cachedCompile($old);

		return $new["updated"] > $old["updated"]
			? $cache->set($cache_key, $new)
			: $new;
	}
}
