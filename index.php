<?php

// Include autoloader and stuff
require 'vendor/autoload.php';
require 'functions.inc';
require 'constants.inc';


// Set error handler
error_reporting(E_ALL);
set_exception_handler(new Error\Handler());


// Remove any default headers, like X-Powered-By
header_remove();


// Enable gzip/deflate
ini_set('zlib.output_compression', 'On');


// Handle request
(new Website(require 'routes.php'))->serve(PATH);
