<?php

class Website
{
	const ROUTE_FILENAME = 'routes.php';
	const CACHE_FILENAME = 'route-cache';

	protected $tokens = [
			':any:' => '(.+)',
			':alpha:' => '([\p{L}_]+)',
			':number:' => '([\p{Nd}]+)',
			':alphanum:'  => '([\p{L}\p{Nd}\p{Pd}_]+)',
		];
	protected $routes_filepath;



	public static function init()
	{
		return new self();
	}

	public function __construct()
	{
		$this->routes_filepath = DOCROOT.self::ROUTE_FILENAME;
	}



	public function serve()
	{
		// Get path
		$path = $_SERVER['PATH_INFO'];

		// Parse path	
		$request = $this->get_parsed_path($path);

		// Execute request
		self::execute($request + [
			'path' => $path,
			'method' => strtolower($_SERVER['REQUEST_METHOD']),
			'handler' => NULL,
			'params' => [],
			]);
	}


	protected static function create_handler($handler)
	{
		if( ! class_exists($handler))
			throw new HttpException("Handler class '$handler' does not exist.");
		return new $handler;
	}


	protected static function execute($request)
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



	protected function get_parsed_path($path)
	{
		$request = $this->parse_path($path);
		
		if($request['handler'] === NULL)
			throw new HttpException('No route found for '.$path, 404);

		return $request;
	}



	protected function parse_path($path)
	{
		$routes = require $this->routes_filepath;

		// Direct match
		if(array_key_exists($path, $routes))
			return ['handler' => $routes[$path]];

		// Pattern match
		foreach($routes as $pattern => $handler)
		{
			if( ! is_string($pattern))
				continue;

			$pattern = strtr($pattern, $this->tokens);

			if(preg_match('#'.$pattern.'/?#Au', $path, $matches))
			{
				unset($matches[0]);

				if(strpos($handler, '$') !== false)
					foreach($matches as $i => $m)
					{
						$handler = str_replace("$$i", ucfirst($m), $handler, $count);
						if($count > 0)
							unset($matches[$i]);
					}

				return ['handler' => $handler, 'params' => $matches];
			}
		}

		// Default route
		if(array_key_exists(0, $routes))
			return ['handler' => $routes[0], 'params' => [$path]];

		// No route found
		return [];
	}
}
