<?php

namespace Controller;
use Session, View, Model;

/**
 * Simple base for JSON API controllers.
 */
class Api extends Secure
{
	public function get($what = null)
	{
		$method = "get_$what";
		if( ! method_exists($this, $method))
			throw new \Error\PageNotFound();

		$data = $this->$method();
		View::json($data)->output();
	}
}
