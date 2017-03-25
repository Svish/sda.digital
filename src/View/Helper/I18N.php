<?php

namespace View\Helper;
use Text;


/**
 * Helper: Translation in Mustache templates.
 */
class I18N
{
	public function __invoke($text, $render = null)
	{
		if($render)
			$text = $render($text);

		return Text::translate($text);
	}
}
