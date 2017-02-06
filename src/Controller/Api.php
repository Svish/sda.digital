<?php

namespace Controller;
use View;

/**
 * Simple base for JSON API controllers.
 */
class Api extends Secure
{
	public function get($what = null)
	{
		$what = str_replace('-', '_', $what);
		$method = "get_$what";

		if( ! method_exists($this, $method))
			throw new \Error\PageNotFound();

		View::json($this->$method())
			->output();
	}
}
