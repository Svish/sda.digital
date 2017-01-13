<?php

/**
 * Checks if TTL has passed.
 */
class Cache_TimeValidator
{
	protected $ttl;

	/**
	 * @param $ttl Max age in seconds.
	 */
	public function __construct($ttl)
	{
		$this->ttl = (int)$ttl;
	}

	/**
	 * @return FALSE if $time is older than TTL.
	 */
	public function __invoke($time, $key)
	{
		return (time() - $time) <= $this->ttl;
	}
}
