<?php


/**
 * Menu hack for selecting active menu item.
 */
class Helper_IsCurrent
{
	public function __invoke($text, Mustache_LambdaHelper $render = null)
	{
		$text = $render ? $render($text) : $text;

		$item = explode('/', $text);
		$path = explode('/', trim($_SERVER['PATH_INFO'], '/'));

		if(reset($item) == reset($path))
			return $text.'" class="current';

		return $text;
	}
}
