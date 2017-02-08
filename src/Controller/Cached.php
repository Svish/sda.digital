<?php

namespace Controller;
use HTTP,Cache;

/**
 * Base controller which handles caching of content
 */
abstract class Cached extends Controller
{
	protected $max_age = 144000; // 4 hours
	
	protected $parameter_whitelist = [];

	private $file;
	private $cache;
	private $cache_key;
	private $cached;

	private $on;


	public function __construct()
	{
		parent::__construct();
		$this->on = 'get' == strtolower($_SERVER['REQUEST_METHOD']);
	}



	public function before(array &$info)
	{
		parent::before($info);

		if( ! $this->on)
			return;

		// Init our cache
		$get = array_whitelist($_GET, $this->parameter_whitelist);
		$get = json_encode($get, JSON_NUMERIC_CHECK);

		$this->cache = new Cache(__CLASS__);
		$this->cache_key = $info['path'].$get;

		// Check cache
		$cached = $this->cache->get($this->cache_key);

		if($cached && $this->cache_valid($cached['time']))
		{
			$this->cached = $cached;
			$info['method'] = 'cached';	
			return;
		}

		// Otherwise, gather regular output
		ob_start();
	}



	protected function cache_valid($cached_time)
	{
		return true;
	}



	public function cached()
	{
		// Get ifs from request headers
		$lmod = @$_SERVER['HTTP_IF_MODIFIED_SINCE'] ?: false;
		$etag = @trim($_SERVER['HTTP_IF_NONE_MATCH']) ?: false;

		// Remove compression method
		// @see https://httpd.apache.org/docs/trunk/mod/mod_deflate.html#deflatealteretag
		$etag = preg_replace('/-.+(?=")/', '', $etag);

		// Respond with 304 and no content if both match
		if($this->cached['lmod'] == $lmod
		&& $this->cached['etag'] == $etag)
		{
			HTTP::set_status(304);
			return;
		}

		// Otherwise resend cached
		http_response_code($this->cached['code']);
		
		foreach($this->cached['headers'] as $h)
			header($h, false);

		header('X-Cache: Hit');
		echo $this->cached['content'];
	}



	public function after(array &$info)
	{
		if( ! $this->cached && $this->on)
		{

			$content = ob_get_clean();
			$time = time() - 2;
			$lmod = gmdate('D, d M Y H:i:s T', $time);
			$etag = '"'.sha1($content).'"';
			$max_age = ENV === 'prod' ? $this->max_age : 5;

			header("Last-Modified: $lmod");
			header("Etag: $etag");
			header("Cache-Control: max-age=$max_age, public");

			$data = [
				'headers' => headers_list(),
				'code' => http_response_code(),
				'time' => $time,
				'lmod' => $lmod,
				'etag' => $etag,
				'length' => strlen($content),
				'content' => $content,
			];

			header('X-Cache: Set');
			$this->cache->set($this->cache_key, $data);
			echo $content;
		}
		
		parent::after($info);
	}

}
