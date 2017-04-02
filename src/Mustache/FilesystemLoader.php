<?php

namespace Mustache;
use Security;


/**
 * Adjusted filesystem loader.
 */
class FilesystemLoader extends \Mustache_Loader_FilesystemLoader
{
	const EXT = '.ms';
	const ACCESS_PRAGMA = '/{{%\s*ACCESS\s*((?<=\s).+)?}}/';


	public function __construct($baseDir, array $options = [])
	{
		parent::__construct($baseDir, $options + [
				'extension' => self::EXT,
			]);
	}

	/**
	 * Load the given file, and process our custom pragma.
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
		$roles = array_map_callbacks($roles, 'trim', 'strtolower');

		// Secure access (throws if no access)
		Security::require($roles);

		// Remove pragma tag
		return null;
	}

}
