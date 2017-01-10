<?php


/**
 * Formats durations.
 */
class Helper_FormatDuration
{
	public function __invoke($d, Mustache_LambdaHelper $render = null)
	{
		$d = new DateInterval($d);
		$text = $d->format('%h:%I:%S');
		$text = ltrim($text, '0:');
		return $render ? $render($text) : $text;
	}
}
