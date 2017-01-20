<?php

// Base paths
define('DOCROOT', realpath(__DIR__).DIRECTORY_SEPARATOR);
define('WEBBASE', $_SERVER['BASE']);
define('WEBROOT', 'http://'.$_SERVER['HTTP_HOST'].WEBBASE);
define('HOST', $_SERVER['HTTP_HOST']);
define('PATH', trim($_SERVER['PATH_INFO'], '/') ?: 'index');


// Environment
define('ENV', $_SERVER['ENV']);

// Language, Encoding and Locales
mb_internal_encoding("UTF-8");
date_default_timezone_set('Europe/Oslo');
define('LANG', 'no');
define('LOCALE', 'nb_NO');
define('LC', setlocale(LC_ALL, 'nb_NO.utf8', 'nb_NO.utf-8', 'nb_NO', 'nor', ''));
setlocale(LC_NUMERIC, 'C');
