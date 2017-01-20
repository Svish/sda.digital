<?php

// Get path from .htaccess GET parameter
$_SERVER['PATH_INFO'] = $_GET['path_uri'] ?? '/';
unset($_GET['path_uri']);


// Include autoloader and stuff
require 'vendor/autoload.php';
require 'constants.inc';


// Fail faster in dev...
if(ENV=='dev')
	set_time_limit(7);


// Set error handler
error_reporting(E_ALL);
set_exception_handler(new Controller_Error());


// Remove default headers like X-Powered-By
header_remove();


// Handle request
(new Website(require 'routes.php'))->serve(PATH);
