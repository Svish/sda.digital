<?php
// Include autoloader and stuff
require 'vendor/autoload.php';
require 'constants.inc';


// Fail faster in dev...
if(ENV=='dev')
	set_time_limit(7);


// Set error handler
error_reporting(E_ALL);
set_exception_handler(new ErrorHandler());


// Remove default headers like X-Powered-By
header_remove();


// Handle request
(new Website(require 'routes.php'))->serve(PATH);
