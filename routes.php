<?php

return [
	
	# Resources
	'/:alpha:.js' => 'Controller_Javascript',
	'/:alpha:.css' => 'Controller_Less',

	# Contact
	'/(contact)' => 'Controller_Contact',

	# User
	'/user/:alpha:' => 'Controller_User',

	# Tools
	'/admin/:alpha:' => 'Controller_Admin_$1',
	'/admin' => 'Controller_Admin',

	# Other
	0 => 'Controller_Page',
];
