<?php

/**
 * Checks if given $mtime is older than $ttl
 */
class Cache_TimeValidator
{
	private $ttl;
	public function __construct($ttl)
	{
		$this->ttl = (int)$ttl;
	}
	public function __invoke($mtime, $key)
	{
		return (time() - $mtime) <= $this->ttl;
	}
}
