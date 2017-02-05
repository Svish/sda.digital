<?php

// Include autoloader and stuff
require 'vendor/autoload.php';
require 'functions.inc';
require 'constants.inc';


// Set error handler
error_reporting(E_ALL);
set_exception_handler(new Error\Handler());


// Remove default headers like X-Powered-By
header_remove();


// Handle request
(new Website(require 'routes.php'))->serve(PATH);
