<?php


/**
 * File helper.
 *
 * @see http://php.net/manual/en/function.fopen.php
 * @see http://php.net/manual/en/function.flock.php
 */
class File
{
	public static function human_size($size, $precision = 2)
	{
		for($i=0; ($size / 1024) > 0.9; $i++, $size /= 1024);
		return round($size, $precision)
			 . ['B','kB','MB','GB','TB','PB','EB','ZB','YB'][$i];
	}



	public static function get($filename, $default = NULL)
	{
		if( ! file_exists($filename))
			return $default;
		
		$fp = fopen($filename, 'r');
		flock($fp, LOCK_SH);
		$contents = fread($fp, filesize($filename));
		flock($fp, LOCK_UN);
		fclose($fp);

		return $contents;
	}



	public static function put($filename, $contents)
	{
		if(empty($filename))
			throw new Exception(__METHOD__.' called with empty filename.');

		self::mkdir(dirname($filename));

		$fp = fopen($filename, 'c');
		flock($fp, LOCK_EX);
		ftruncate($fp, 0);
		fwrite($fp, $contents);
		fflush($fp);
		flock($fp, LOCK_UN);
		fclose($fp);

		return $contents;
	}



	public static function mkdir($dir)
	{
		if( ! is_dir($dir))
		{
			// https://en.wikipedia.org/wiki/Chmod#System_call
			@mkdir($dir, 06750, true);
			@chmod($dir, 06750);
		}
		return $dir;
	}



	public static function rdelete($directory, $keep_self = false)
	{
		if( ! is_dir($directory))
			return;

		$it = new RecursiveDirectoryIterator($directory,FilesystemIterator::SKIP_DOTS);
		$it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
 
		foreach($it as $file)
			if($file->isDir())
				@rmdir($file->getRealPath());
			else
				@unlink($file->getRealPath());

		if( ! $keep_self)
			@rmdir($directory);
	}
}
