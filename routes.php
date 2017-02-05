<?php

return [
	
	# Resources
	'js/(:alpha:.js)' => '\Controller\\Javascript',
	'theme/(:alpha:.css)' => '\Controller\\Less',


	# Slugs
	':alpha:/:digit:/:alpha:' => function (array &$request)
	{
		// TODO: Does this work?
		$params = explode('/', trim($request['path'], '/'));
		$handler = array_shift($params);
		$request['params'] = $params;

		var_dump(get_defined_vars());
		
		return $handler;
	},

	'.+/api/:alpha:' => function (array $request)
	{
		$h = explode('/', trim($request['path'], '/'));
		array_pop($h);
		$h = 'Controller\\'.implode('\\', array_map('ucfirst', $h));

		return $h;
	},

	# Any other pages
	0 => function (array $request)
	{
		$h = explode('/', 'controller/'.trim($request['path'], '/'));
		$h = implode('\\', array_map('ucfirst', $h));
		return class_exists($h)
			? $h
			: 'Controller\\Page';
	},
];
