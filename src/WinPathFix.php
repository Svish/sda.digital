<?php

define('IS_WIN', stripos(PHP_OS, 'win') === 0);

/**
 * Methods for fixing paths if on windows.
 */
trait WinPathFix
{
	private static function to_win($path)
	{
		return IS_WIN ? utf8_decode($path) : $path;
	}
	
	private static function from_win($path)
	{
		return IS_WIN ? utf8_encode($path) : $path;
	}
}
