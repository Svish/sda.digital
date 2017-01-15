<?php

return [
	
	# Resources
	'/js/(:alpha:.js)' => 'Controller_Javascript',
	'/theme/(:alpha:.css)' => 'Controller_Less',


	# Slugs
	'/:alpha:/:digit:/:alpha:' => function (array &$request)
	{
		// TODO: Does this work?
		$params = explode('/', trim($request['path'], '/'));
		$handler = array_shift($params);
		$request['params'] = $params;

		var_dump(get_defined_vars());
		
		return $handler;
	},


	# Any other pages
	0 => function (array $request)
	{
		$h = explode('/', trim($request['path'], '/'));
		$h = 'Controller_'.implode('_', array_map('ucfirst', $h));
		return class_exists($h)
			? $h
			: 'Controller_Page';
	},
];
