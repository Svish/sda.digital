<?php

class Website
{
	protected $tokens = [
			':any:' => '(.+)',
			':alpha:' => '([\p{L}_]+)',
			':number:' => '([\p{Nd}]+)',
			':alphanum:'  => '([\p{L}\p{Nd}\p{Pd}_]+)',
		];
	protected $routes;
	protected $path;


	public function __construct(array $routes)
	{
		$this->routes = $routes;
	}


	public function serve($path = null)
	{
		// Get path
		$this->path = $path === null ? $_SERVER['PATH_INFO'] : $path;

		// Parse path
		$request = $this->get_parsed_path($this->path);

		// Execute request
		$this->execute($request + [
			'path' => $this->path,
			'method' => strtolower($_SERVER['REQUEST_METHOD']),
			'handler' => NULL,
			'params' => [],
			]);
	}


	protected function execute($request)
	{
		// Try create handler
		try
		{
			$handler = self::create_handler($request['handler']);
		}
		catch(HttpException $e)
		{
			throw new HttpException("Page '{$request['path']}' not found", 404, $e);
		}

		// Call handler::before
		if( method_exists($handler, 'before'))
			call_user_func_array([$handler, 'before'], [&$request]);

		// Default to handler::get if actual method does not exist
		if( ! method_exists($request['handler'], $request['method']))
			$request['method'] = 'get';

		// Call handler::method
		call_user_func_array([$handler, $request['method']], $request['params']);

		// Call handler::after
		if( method_exists($handler, 'after'))
			call_user_func_array([$handler, 'after'], [&$request]);
	}



	protected function create_handler($handler)
	{
		if( ! class_exists($handler))
			throw new HttpException("Handler class '$handler' does not exist.");
		return new $handler;
	}



	protected function get_parsed_path($path)
	{
		$request = $this->parse_path($path);
		
		if($request['handler'] === NULL)
			throw new HttpException('No route found for '.$path, 404);

		return $request;
	}



	protected function parse_path($path)
	{
		// 0: Check for direct match
		if(array_key_exists($path, $this->routes))
			return ['handler' => $this->routes[$path]];

		// 1: Check for regex matches
		foreach($this->routes as $pattern => $handler)
		{
			if( ! is_string($pattern))
				continue;

			$pattern = strtr($pattern, $this->tokens);

			if(preg_match('#'.$pattern.'/?#Au', $path, $matches))
			{
				unset($matches[0]);

				if(is_string($handler) && strpos($handler, '$') !== false)
					foreach($matches as $i => $m)
					{
						$handler = str_replace("$$i", ucfirst($m), $handler, $count);
						if($count > 0)
							unset($matches[$i]);
					}

				return ['handler' => $handler, 'params' => $matches];
			}
		}

		// 2: Check for default route
		if(array_key_exists(0, $this->routes))
			return ['handler' => $this->routes[0], 'params' => [$path]];

		// 3: None found
		return [];
	}
}
