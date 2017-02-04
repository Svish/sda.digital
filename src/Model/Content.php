<?php

namespace Model;
use Model;

/**
 * User model for handling logins, etc.
 */
class Content extends Model
{
	const DIR = '_'.DIRECTORY_SEPARATOR;


	public static function add(array $content)
	{
		var_dump($content);
		// Begin transaction
		// Add speakers
		// Add content
		// Add and copy files
		// (Optionally) Add series
		// End transaction
			// Delete copied if failed
			// Delete original if ok
	}


	/*
var_dump(stat(utf8_decode('X:\dev\www\sda.digital\_new\I Naturens Tempel\02 Salige er de som hungrer og tørster etter rettferdighet.mp3')));
	*/

}
