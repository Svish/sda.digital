<?php

namespace DB;
use DB, PDO, Cache, Message;

/**
 * Performs DB migrations.
 */
class Migrator
{
	const DIR = SRC.DIRECTORY_SEPARATOR.'_schema'.DIRECTORY_SEPARATOR;


	public function __construct(PDO $pdo)
	{
		// Try get version number
		try
		{
			$q = $pdo->query('SELECT * FROM version');
			$current = (int) $q->fetchColumn(0);
			$q->closeCursor();
		}
		catch(PDOException $e)
		{
			// Create version table
			$pdo->exec('CREATE TABLE IF NOT EXISTS `version` (
				`version` int(10) UNSIGNED NOT NULL
				) ENGINE=InnoDB');
			$pdo->exec('INSERT INTO `version` VALUES(0)');
			$current = 0;
		}

		// Get migration files, sorted
		$files = glob(self::DIR.'*.sql', GLOB_NOSORT);
		natsort($files);

		// Filter out new versions
		foreach($files as $key => &$f)
		{
			$version = (int) str_replace(self::DIR, NULL, $f);
			if($version <= $current)
			{
				unset($files[$key]);
				continue;	
			}

			$f = [
				'file' => $f,
				'version' => $version,
				];
		}

		// If any new migration files
		if($files)
		{
			// Disable key checks while migrating
			$pdo->exec("SET foreign_key_checks = 0");

			// Process each file
			foreach($files as $file)
			{
				extract($file);
				try
				{
					// Get SQL script, without # comments
					$script = preg_replace('/#.++/', NULL, file_get_contents($file));

					// Split into queries
					$queries = preg_split('/;\s*$/m', $script, -1, PREG_SPLIT_NO_EMPTY);
					$queries = array_map('trim', $queries);
					$queries = array_filter($queries);

					// Run each query
					foreach($queries as $q)
						$pdo->exec($q);

					// Run <version>.php if it exists
					if(file_exists(self::DIR.$version.'.php'))
						require self::DIR.$version.'.php';

					// Update version table
					$pdo->exec('UPDATE version SET version = '.$version);
				}
				catch(PDOException $e)
				{
					throw new Exception('DB Migration failed.', 0, $e);
				}
			}

			// Re-enable key checks
			$pdo->exec("SET foreign_key_checks = 1");
			
			// Clear DB cache
			$cache = new Cache(DB::class, null);
			$cache->clear();

			// Add info message
			Message::ok('db-migrated', $version);			
		}
	}

}
