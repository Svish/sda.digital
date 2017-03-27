<?php

namespace View\Helper;

/**
 * Helper: Returns the first line of text.
 */
class FirstLine
{
	public function __invoke($text, $render = null)
	{
		if($render)
			$text = $render($text);

		return explode("\r\n", $text, 2)[0];
	}
}
