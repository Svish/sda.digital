<?php

/**
 * Returns HTML for Clicky tracking code.
 *
 * @see https://clicky.com
 */
class Helper_Clicky
{
	public function __invoke()
	{
		return Mustache::engine()->render('clicky', new Clicky());
	}
}
