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



	public function __construct(array $routes)
	{
		$this->routes = $routes;
	}



	public function serve($path)
	{
		// Find route
		$route = $this->find_route($path);

		// Execute request
		$this->execute($route + [
			'path' => $path,
			'method' => strtolower($_SERVER['REQUEST_METHOD']),
			'handler' => null,
			'params' => [],
			]);
	}



	protected function execute($request)
	{
		// Try create handler
		try
		{
			if(is_callable($request['handler']))
				$request['handler'] = $request['handler']($request);

			$handler = self::create_handler($request['handler']);
		}
		catch(HttpException $e)
		{
			throw new Error\HttpException("Page '{$request['path']}' not found", 404, $e);
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
            throw new Error\HttpException("Handler class '$handler' does not exist.");
        return new $handler;
    }



	protected function find_route($path)
	{
		$route = $this->parse_path($path);
		
		if($route['handler'] === NULL)
			throw new Error\HttpException("No page found for '$path'", 404);

		return $route;
	}



	protected function parse_path($path)
	{
		// 0: Check for direct match
		if(array_key_exists($path, $this->routes))
			return ['handler' => $this->routes[$path], 'route' => $path];

		// 1: Check for regex matches
		foreach($this->routes as $pattern => $handler)
		{
			if( ! is_string($pattern))
				continue;

			$regex = strtr($pattern, $this->tokens);

			if(preg_match('#'.$regex.'/?#Au', $path, $matches))
			{
				unset($matches[0]);
				return ['handler' => $handler, 'params' => $matches, 'route' => $pattern];
			}
		}

		// 2: Check for default route
		if(array_key_exists(0, $this->routes))
			return ['handler' => $this->routes[0], 'route' => 0];

		// 3: None found
		return [];
	}
}
