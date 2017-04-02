<?php

namespace View\Helper;
use Config, Model, Mustache;

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
		{
			$config['user'] = Model::users()->logged_in();
			
			return Mustache::engine()
				->render('clicky', $config);
		}
	}
}
