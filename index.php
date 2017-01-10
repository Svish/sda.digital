<?php

// Include autoloader and stuff
require 'vendor/autoload.php';
require 'constants.inc';

if(ENV=='dev')
	set_time_limit(10);


// Error handling
error_reporting(E_ALL);
// TODO: set_exception_handler(new Controller_Error());
// http://php.net/manual/en/language.errors.php7.php


// Get path from htaccess parameter
$_SERVER['PATH_INFO'] = isset($_GET['path_uri']) ? $_GET['path_uri'] : '/';
unset($_GET['path_uri']);


// Remove default headers like X-Powered-By
header_remove();

// Handle request
Website::init()->serve();
