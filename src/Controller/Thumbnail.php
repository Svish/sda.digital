<?php

/**
 * Serve thumbnails of images.
 */
class Controller_Thumbnail extends CachedController
{
	protected $max_age = 172800; // 48 hours

	private $whitelist = [
		[100,150], // Index: Album cover
		[120,250], // Music: Listing, cover
		[450,120], // Members: Listing
		[350,250], // Members: Details
		[400,500], // Music: Album, cover and inlay
		[700,700], // Choir: Large image
		[960,960], // Header
		];
	private $file;



	public function before(array &$info)
	{
		$this->file = $this->find_file($info['params'][3]) ?: DOCROOT.'_/blank.png';
		if( ! $this->file)
			throw new HttpException('Image not found', 404);

		if( ! in_array([$info['params'][1], $info['params'][2]], $this->whitelist))
			throw new HttpException('Requested size not in whitelist', 400);

		parent::before($info);
	}

	
	protected function cache_valid($cached_time)
	{
		return parent::cache_valid($cached_time)
		   and $cached_time >= filemtime($this->file);
	}

	public function get($w, $h, $path)
	{
		Session::close();
		
		set_time_limit(60);
		ini_set('memory_limit','256M');
		ini_set('gd.jpeg_ignore_warning', '1');

		$i = new PHPImage($this->file);
		$i->resize((int)$w, (int)$h, false, true);
		$i->setOutput('jpg', 90);
		$i->show();
	}



	private function find_file($path)
	{
		$files = glob(DOCROOT."{_,data}/$path", GLOB_BRACE);
		foreach($files as $file)
			return $file;
	}
}
