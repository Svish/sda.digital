<?php
/**
 * PDO helper
 *
 * @see https://phpdelusions.net/pdo
 */
class DB
{
	public static function exec($statement)
	{
		return self::instance()->exec($statement);
	}

	public static function prepare($statement)
	{
		return new Query(self::instance()->prepare($statement));
	}

	public static function query($statement)
	{
		return new Query(self::instance()->query($statement));
	}



	private static $pdo = NULL;

	private function __construct() { }
	private function __clone() { }

	public static function instance()
	{
		if (!self::$pdo)
		{
			$config = Config::database()[ENV];
			$timezone = date_default_timezone_get();

			self::$pdo = new PDO
			(
				$config['dsn'],
				$config['username'],
				$config['password'],
				[
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SQL_MODE='TRADITIONAL', TIME_ZONE='{$timezone}';",
				]
			);
			self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

			if('mysql' == self::$pdo->getAttribute(PDO::ATTR_DRIVER_NAME))
				self::$pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

			self::migrate();
		}

		return self::$pdo;
	}



	const DIR = __DIR__.DIRECTORY_SEPARATOR.'_schema'.DIRECTORY_SEPARATOR;

	public static function migrate()
	{
		// Try get version number
		try
		{
			$current = (int) DB::query('SELECT * FROM version')
				->fetchColumn();
		}
		catch(PDOException $e)
		{
			// Create version table
			DB::exec('CREATE TABLE IF NOT EXISTS `version` (`version` int(10) UNSIGNED NOT NULL) ENGINE=InnoDB');
			DB::exec('INSERT INTO `version` VALUES(0)');
			$current = 0;
		}

		foreach(glob(self::DIR.'*.sql') as $m)
		{
			// Get version number from filename
			$version = (int) str_replace(self::DIR, NULL, $m);

			if($version > $current)
			{
				try
				{
					// Get SQL script, without # comments
					$script = preg_replace('/#.++/m', NULL, file_get_contents($m));

					// Split into queries
					$queries = preg_split('/;\s*$/m', $script, -1, PREG_SPLIT_NO_EMPTY);

					// Run each query
					foreach($queries as $q)
						if(trim($q) != '')
							DB::exec($q);

					// Run <version>.php if it exists
					if(file_exists(self::DIR.$version.'.php'))
						require self::DIR.$version.'.php';

					// Update version table
					DB::query('UPDATE version SET version = '.$version);

					// Clear DB cache
					$cache = new Cache(DB::class);
					$cache->clear();
				}
				catch(PDOException $e)
				{
					throw new HttpException('DB Migration failed.', 500, $e);
				}
			}
		}


	}
}
