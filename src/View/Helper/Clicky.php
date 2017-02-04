<?php

namespace View\Helper;
use Mustache, Clicky as C;

/**
 * Returns HTML for Clicky tracking code.
 *
 * @see https://clicky.com/stats/prefs-tracking-code
 */
class Clicky
{
	public function __invoke()
	{
		return Mustache::engine()->render('clicky', new C());
	}
}
