<?php

namespace View\Helper;
use Mustache_LambdaHelper;

/**
 * Helper: Returns the first line of text.
 */
class FirstLine
{
	public function __invoke($text, Mustache_LambdaHelper $render = null)
	{
		// Get text up till first <br>
		$text = explode('<br>', $text, 2)[0];
		return $render ? $render($text) : $text;
	}
}
