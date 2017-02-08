<?php

namespace Controller;
use Config, Cache, HTTP;
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
		$this->compile();

		parent::before($info);
	}



	public function get()
	{
		header('Content-Type: text/css; charset=utf-8');
		echo implode("\r\n", [
			"/**",
			" * Compiled: ".date('Y-m-d H:i:s', $new['updated'] ?? time()),
			" * By: ".__CLASS__,
			" * Using: http://leafo.net/lessphp",
			" * Took: " . number_format(microtime(TRUE) - $this->time, 3),
			" */",
			$this->data['compiled'],
		]);
	}



	protected function cache_valid($cached_time)
	{
		return parent::cache_valid($cached_time)
		   and $cached_time >= $this->data['updated'];
	}


	private function compile()
	{
		$cache = new Cache(__CLASS__);
		$cache_key = basename($this->file).'c';

		// Get cached if exists
		$old = $cache->get($cache_key, ['root' => $this->file, 'updated' => 0]);

		// Do a cached compile
		try
		{
			$this->time = microtime(TRUE);
			$less = new lessc;
			$less->setFormatter('compressed');
			$new = $less->cachedCompile($old);
		}
		catch(\Exception $e)
		{
			HTTP::plain_exit(500, $e->getMessage());
		}

		// Set if updated
		$this->data = $new['updated'] > $old['updated']
			? $cache->set($cache_key, $new)
			: $new;
	}
}
