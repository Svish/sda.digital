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
	use Instance;

	public static function get($path)
	{
		return self::instance()->file($path);
	}
	

	private $finfo;
	private $map;
	private function __construct()
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
			$type = $this->cache->get('map')[pathinfo($path, PATHINFO_EXTENSION)] ?? $type;

		return [
			'type' => $type,
			'encoding' => $this->finfo->file($path, FILEINFO_MIME_ENCODING),
			'description' => $this->finfo->file($path, FILEINFO_NONE),
		];
	}



	const SOURCE = 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';

	/**
	 * Load map of ext => mime/type
	 */
	public static function load_map()
	{
		yield 'map' => self::mime_types();
	}
	private static function mime_types()
	{
		$source = file_get_contents(self::SOURCE);
		preg_match_all('/^([^#\s]+)\s+(.+)/m', $source, $result, PREG_SET_ORDER);

		foreach($result as $match)
			foreach(preg_split('/\s+/', $match[2]) as $ext)
				yield $ext => $match[1];
	}
}
