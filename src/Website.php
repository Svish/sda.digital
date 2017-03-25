<?php

class Website
{
	protected $tokens = [
			':any:' => '(.+)',
			':alpha:' => '([\p{L}_-]+)',
			':number:' => '([\p{Nd}]+)',
			':alphanum:'  => '([\p{L}\p{Nd}\p{Pd}_]+)',
		];
	protected $routes;
	protected $path;


	public function __construct(array $routes, string $path)
	{
		$this->routes = $routes;
		$this->path = $path;
	}



	public function serve()
	{
		// Find route
		$route = $this->find_route($this->path);

		// Execute request
		$this->execute($route + [
			'path' => $this->path,
			'method' => strtolower($_SERVER['REQUEST_METHOD']),
			'handler' => null,
			'params' => [],
			]);
	}



	protected function execute($request)
	{
		// If callable, get handler from it
		if(is_callable($request['handler']))
			$request['handler'] = $request['handler']($request);

		// Try create the handler
		$handler = self::create_handler($request['handler'], $request['path']);

		// Call handler::before
		if( method_exists($handler, 'before'))
			$handler->before($request);

		// Call handler::method
		call_user_func_array([$handler, $request['method']], $request['params']);

		// Call handler::after
		if( method_exists($handler, 'after'))
			$handler->after($request);
	}



	protected function create_handler($handler)
	{
		try
		{
			if( ! class_exists($handler))
				 throw new Exception("Handler class '$handler' does not exist.");
		}
		catch(Exception $e)
		{
			throw new Error\PageNotFound($this->path, $e);
		}
		return new $handler;
	}



	protected function find_route($path)
	{
		$route = $this->parse_path($path);
		Log::group();
		Log::trace_raw('Path:', $path);
		Log::trace_raw('Route:', $route);
		Log::groupEnd();
		
		if($route['handler'] === null)
			throw new Error\PageNotFound($path);

		return $route;
	}



	protected function parse_path($path)
	{
		// 0: Check for direct match
		if(array_key_exists($path, $this->routes))
			return ['route' => $path, 'handler' => $this->routes[$path]];

		// 1: Check for regex matches
		foreach($this->routes as $pattern => $handler)
		{
			if( ! is_string($pattern))
				continue;

			$regex = strtr($pattern, $this->tokens);

			if(preg_match('#'.$regex.'/?#Au', $path, $matches))
			{
				unset($matches[0]);
				return ['route' => $pattern, 'handler' => $handler, 'params' => $matches];
			}
		}

		// 2: Check for default route
		if(array_key_exists(0, $this->routes))
			return ['route' => 0, 'handler' => $this->routes[0]];

		// 3: None found
		return [];
	}
}
