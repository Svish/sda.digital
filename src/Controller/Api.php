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
		return $this->process($what);
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

		$input = file_get_contents('php://input');
		
		$data = json_decode($input, true);
		$data = $this->$method($data === null ? $input : $data);

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
