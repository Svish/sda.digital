<?php

namespace View\Helper;
use Mustache_LambdaHelper;
use Markdown;

/**
 * Helper: Markdown render.
 */
class Md
{
	public function __invoke($text, Mustache_LambdaHelper $render = null)
	{
		if($render)
			$text = $render($text);

		return Markdown::render($text);
	}
}
