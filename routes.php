<?php

return [
	
	# Resources
	'/:alpha:.js' => 'Controller_Javascript',
	'/:alpha:.css' => 'Controller_Less',

	# Contact
	'/(contact)' => 'Controller_Contact',


	# Tools
	'/admin/:alpha:/:alpha:' => 'Controller_Admin_$1',
	'/admin' => 'Controller_Admin',

	# User
	'/:alpha:/:alpha:' => 'Controller_$1_$2',

	# Other
	0 => 'Controller_Page',
];
