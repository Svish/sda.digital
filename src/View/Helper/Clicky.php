<?php

namespace View\Helper;
use Config, Mustache;

/**
 * Helper: Returns HTML for Clicky tracking code.
 *
 * @see https://clicky.com/stats/prefs-tracking-code
 */
class Clicky
{
	public function __invoke()
	{
		$config = Config::clicky()[ENV] ?? false;

		if($config)
			return Mustache::engine()
				->render('clicky', $config);
	}
}
