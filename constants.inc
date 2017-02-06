<?php

// System paths
define('ROOT', realpath(__DIR__).DIRECTORY_SEPARATOR);
define('SRC', ROOT.'src'.DIRECTORY_SEPARATOR);

// Web paths
define('SCHEME', empty($_SERVER['HTTPS']) || $_SERVER['HTTPS']=='off' ? 'http' : 'https');
define('HOST', $_SERVER['HTTP_HOST']);
define('WEBBASE', $_SERVER['BASE']);
define('WEBROOT', SCHEME.'://'.HOST.WEBBASE);

// Current path
$path = $_GET['path_uri'] ?? '';
$path = trim($path, '/');
define('PATH', $path ?: 'index');
unset($_GET['path_uri']);

// Environment
define('ENV', $_SERVER['ENV']);

// Language, Encoding and Locales
mb_internal_encoding("UTF-8");
date_default_timezone_set('Europe/Oslo');
define('LANG', 'no');
define('LOCALE', 'nb_NO');
define('LC', setlocale(LC_ALL, 'nb_NO.utf8', 'nb_NO.utf-8', 'nb_NO', 'nor', ''));
setlocale(LC_NUMERIC, 'C');
