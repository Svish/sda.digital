<?php

return [
	
	# Resources
	'js/(:any:.js)$' => '\Controller\\Javascript',
	'theme/(:any:\.css)$' => '\Controller\\Less',
	'theme/icon/(:any:\.svg)' => '\Controller\\Svg',


	# Content slugs (type/id/slug)
	# @see src/Data/UrlEntity
	':alpha:/([\p{Nd}]+|new|my-fresh)(?:/:any:)?' => function (array &$request)
	{
		$type = array_shift($request['params']);
		$type = ucfirst($type);
		return "Controller\\$type";
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
