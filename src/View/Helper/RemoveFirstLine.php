<?php

namespace View\Helper;

/**
 * Helper: Removes the first line.
 */
class RemoveFirstLine
{
	public function __invoke($text, $render = null)
	{
		if($render)
			$text = $render($text);

		$text = explode("\r\n", $text);
		array_shift($text);
		return implode("\r\n", $text);
	}
}
