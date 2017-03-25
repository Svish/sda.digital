<?php

namespace View\Helper;
use ReflectionMethod;
use DateTime;
use Valid;

/**
 * Helper: Slightly date formatter.
 */
class Df
{
	private $cache = [];

	public function __get($key)
	{
		return $this->cache[$key];
	}

	public function __isset($key)
	{
		if(array_key_exists($key, $this->cache))
			return true;

		if(method_exists($this, "_$key"))
		{
			$r = new ReflectionMethod($this, "_$key");
			$this->cache[$key] = $r->getClosure($this);
			return true;
		}

		return false;
	}


	private function _full($datetime, $render = null)
	{
		if($render)
			$datetime = $render($datetime);
		$date = new DateTime($datetime);

		$format = '%#d. %B %Y, %R';
		return strftime($format, $date->getTimestamp());
	}


	private function _iso($datetime, $render = null)
	{
		if($render)
			$datetime = $render($datetime);
		$date = new DateTime($datetime);
		return $date->format(DateTime::ATOM);
	}


	private function _flex($datetime, $render = null)
	{
		if($render)
			$datetime = $render($datetime);

		
		// Uknown
		if( ! $datetime)
			return 'Ukjent';

		// Year only
		if(Valid::integer($datetime))
			return $datetime;

		if( ! preg_match('/^'.Valid::FLEXI_TIME.'$/', $datetime, $x))
			return $datetime;

		extract($x);
		$format = '%#d. %B %Y, %H:%M';

		// Unknown minute
		if( ! ($min ?? null))
			$format = str_replace('%M', '00', $format);

		// Unknown hour
		if( ! ($hour ?? null))
			$format = substr($format, 0, -7);

		// Unknown day
		if( ! ($day ?? null))
			$format = substr($format, 5);

		$date = new DateTime($datetime);
		return strftime($format, $date->getTimestamp());
	}
}
