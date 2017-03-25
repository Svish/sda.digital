<?php

// Include autoloader and stuff
require 'vendor/autoload.php';
require 'functions.inc';
require 'constants.inc';


if(false)
{
	error_reporting(E_ALL);
	$c = new Data\Content;
	$c->file_list = [new Data\File, new Data\File];
	echo '<pre>'.json_encode($c, JSON_PRETTY_PRINT).'</pre>';
	echo '<hr>';

	unset($c->file_list);
	echo '<pre>'.json_encode([$c, $x], JSON_PRETTY_PRINT).'</pre>';
	echo '<hr>';

	$x->content = $c;
}

// Set error handler
error_reporting(E_ALL);
$eh = new Error\Handler();
set_exception_handler($eh);
set_error_handler([$eh, 'error']);


// Remove any default headers, like X-Powered-By
header_remove();


// Enable gzip/deflate
ini_set('zlib.output_compression', 'On');


// Handle request
(new Website(require 'routes.php', PATH))
	->serve();
