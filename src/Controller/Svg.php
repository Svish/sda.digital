<?php

namespace Controller;
use HTTP;

/**
 * Handles serving of SVG files.
 */
class Svg extends Cached
{
	const DIR = SRC.'_icons'.DIRECTORY_SEPARATOR;
	const OPTS = ['fill'];

	protected $parameter_whitelist = self::OPTS;

	private $files;
	private $file;
	public function __construct()
	{
		parent::__construct();
		

		// Add single files
		$this->files = glob(self::DIR.'*.svg');
	}

	public function before(array &$info)
	{
		$this->file = realpath(self::DIR.$info['params'][1]);

		if( ! in_array($this->file, $this->files))
			HTTP::plain_exit(404, $info['path']);

		parent::before($info);
	}



	protected function cache_valid($cached_time)
	{
		return parent::cache_valid($cached_time)
		   and $cached_time >= filemtime($this->file);
	}
	


	public function get($filename)
	{
		// Load file
		$doc = new \DOMDocument;
		$doc->load($this->file);

		// Set id attribute
		$svg = $doc->documentElement;
		$svg->setAttribute('id', $filename);

		// Extra stuff
		$opts = array_whitelist($_GET ?? [], self::OPTS);
		foreach($opts as $a => $v)
			$svg->setAttribute($a, $v);

		// Output
		header('Content-Type: image/svg+xml; charset=utf-8');
		$doc->save('php://output');
	}
}
