<?php

namespace View\Helper;
use Mustache_LambdaHelper;

/**
 * Helper: Strips protocol from URL for nicer label.
 */
class UrlLabel
{
	public function __invoke($url, Mustache_LambdaHelper $render = null)
	{
		if($render)
			$url = $render($url);
	}
}
