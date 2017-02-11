<?php

namespace Mustache;

use Controller\Secure;

/**
 * Adjusted filesystem loader.
 */
class FilesystemLoader extends \Mustache_Loader_FilesystemLoader
{
	const ACCESS_PRAGMA = '/{{%\s*ACCESS\s*((?<=\s).+)?}}/';


	/**
	 * Load the given file, and look for our custom pragmas.
	 */
	protected function loadFile($name)
	{
		$contents = parent::loadFile($name);

		return preg_replace_callback(self::ACCESS_PRAGMA, [$this, 'roles'], $contents);
	}

	protected function roles($roles)
	{
		// Split roles into array
		$roles = preg_split('/\s*,\s*/', $roles[1] ?? '', null, PREG_SPLIT_NO_EMPTY);
		$roles = array_multimap(['trim', 'strtolower'], $roles);

		// Secure access (throws if no access)
		Secure::access($roles);

		// Remove pragma tag
		return null;
	}

}
