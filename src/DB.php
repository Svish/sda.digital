<?php

use DB\PDO;
use DB\Query;
use Cache\PreCheckedCache;


/**
 * PDO helper
 *
 * @see https://phpdelusions.net/pdo
 */
class DB
{
	use Instance;


	public static function exec($statement)
	{
		return self::instance()->pdo->exec($statement);
	}

	public static function prepare($statement)
	{
		return new Query(self::instance()->pdo->prepare($statement), self::instance()->pdo);
	}
	
	public static function query($statement)
	{
		return new Query(self::instance()->pdo->query($statement), self::instance()->pdo);
	}
	
	public static function table_info(string $table_name)
	{
		return self::instance()->cache->get($table_name, function($key)
			{
				throw new Exception("No table info found for table '$key'");
			});
	}



	private static $_migrator;
	private $_pdo;

	public function __construct()
	{
		$config = Config::database()[ENV];
		$timezone = date_default_timezone_get();

		// Create PDO object
		$this->pdo = new PDO
		(
			$config['dsn'],
			$config['username'],
			$config['password'],
			[
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET
					SQL_MODE='TRADITIONAL',
					TIME_ZONE='{$timezone}'
					;",
			]
		);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

		// Migrate if we haven't
		if( ! self::$_migrator)
			self::$_migrator = new DB\Migrator($this->pdo);

		// Get cache
		// HACK: Make sure these classes mtimes are included in cache check.
		class_exists('DB\\Query');
		class_exists('DB\\Valid');
		$this->cache = new PreCheckedCache(self::class, 
			new DB\TableInfoLoader($this->pdo));
	}
}
