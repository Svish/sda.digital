<?php

return [
	
	# Resources
	'js/(:any:.js)$' => '\Controller\\Javascript',
	'theme/(:any:.css)$' => '\Controller\\Less',


	# Content slugs
	':alpha:/:digit:/:alpha:' => function (array &$request)
	{
		// TODO: Does this work?
		$params = explode('/', $request['path']);
		$handler = array_shift($params);
		$request['params'] = $params;

		var_dump(get_defined_vars()); exit('did it?');
		
		return $handler;
	},

	# APIs
	'.+/api/:alpha:' => function (array $request)
	{
		$path = ucwords($request['path'], '/-');
		$path = str_replace('/', '\\', $path);
		$handler = substr($path, 0, strrpos($path, '\\'));
		return "Controller\\$handler";
	},

	# Any other pages
	0 => function (array $request)
	{
		$path = ucwords($request['path'], '/-');
		$path = str_replace(['-', '/'], ['', '\\'], $path);
		$handler = "Controller\\$path";
		return class_exists($handler)
			? $handler
			: 'Controller\\Page';
	},
];
