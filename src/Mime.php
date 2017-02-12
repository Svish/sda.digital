<?php

/**
 * Mime type helper.
 *
 * Uses finfo class and public Apache HTTP mime.types file.
 *
 * @see http://php.net/manual/en/class.finfo.php
 */
class Mime
{
	const MIME_TYPES = 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';

	private static $instance;
	public static function get($path)
	{
		if(!self::$instance)
			self::$instance = new self;
		return self::$instance->file($path);
	}
	

	private $finfo;
	private $map;
	public function __construct()
	{
		$this->finfo = new finfo();
		$this->cache = new Cache\PreCheckedCache(__CLASS__, [__CLASS__, 'load_map']);
	}


	/**
	 * Returns type, encoding and description of file.
	 *
	 * @param path Path to file.
	 */
	public function file($path)
	{
		$type = $this->finfo->file($path, FILEINFO_MIME_TYPE);

		// HACK: Try use extension via map if finfo "fails"
		if($type == 'application/octet-stream')
			$type = $this->cache->get(pathinfo($path, PATHINFO_EXTENSION), $type);

		return [
			'type' => $type,
			'encoding' => $this->finfo->file($path, FILEINFO_MIME_ENCODING),
			'desc' => $this->finfo->file($path, FILEINFO_NONE),
		];
	}


	/**
	 * Loads, parses and yields ext => mime-type map.
	 */
	public static function load_map()
	{
		$source = file_get_contents(self::MIME_TYPES);
		preg_match_all('/^([^#\s]+)\s+(.+)/m', $source, $result, PREG_SET_ORDER);

		foreach($result as $match)
			foreach(preg_split('/\s+/', $match[2]) as $ext)
				yield $ext => $match[1];
	}
}
