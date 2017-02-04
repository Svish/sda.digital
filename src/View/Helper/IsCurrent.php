<?php

namespace View\Helper;
use Mustache_LambdaHelper;


/**
 * Menu hack for selecting active menu item.
 */
class IsCurrent
{
	public function __invoke($text, Mustache_LambdaHelper $render = null)
	{
		$text = $render ? $render($text) : $text;

		$item = explode('/', $text);
		$path = explode('/', trim(PATH, '/'));

		if(reset($item) == reset($path))
			return $text.'" class="current';

		return $text;
	}
}
