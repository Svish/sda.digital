<?php

namespace Controller;
use View;

/**
 * Simple base for JSON API controllers.
 */
class Api extends Secure
{
	
	public final function get($what = null)
	{
		$method = self::method($what);
		View::json($this->$method())
			->output();
	}


	public final function delete($what = null)
	{
		return $this->process($what);
	}


	public final function put($what = null)
	{
		return $this->process($what);
	}


	public final function post($what = null)
	{
		return $this->process($what);
	}


	private final function process($what = null)
	{
		$method = self::method($what);

		$data = file_get_contents('php://input');
		$data = json_decode($data, true);
		$data = $this->$method($data);

		View::json($data)
			->output();
	}


	private function method(string $name): string
	{
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		$method .= str_replace('-', '_', "_$name");

		if( ! method_exists($this, $method))
			throw new \Error\PageNotFound();

		return $method;
	}
}
