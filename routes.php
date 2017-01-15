<?php

return [
	
	# Resources
	'/js/(:alpha:.js)' => 'Controller_Javascript',
	'/theme/(:alpha:.css)' => 'Controller_Less',

	# Other
	0 => function (array $request)
	{
		$h = explode('/', trim($request['path'], '/'));
		$h = implode('_', array_map('ucfirst', $h));
		
		return class_exists($h)
			? $h
			: 'Controller_Page';
	}
];
