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
		return self::instance()->pdo->exec($statement);
	}
	public static function prepare($statement)
	{
		return new Query(self::instance()->pdo->prepare($statement));
	}
	public static function query($statement)
	{
		return new Query(self::instance()->pdo->query($statement));
	}
	public static function getTableInfo(string $table_name)
	{
		return self::instance()->cache->get($table_name, function()
			{
				throw new Exception('DB::getTableInfo called without preloading?');
			});
	}

	public static function instance()
	{
		if (!self::$instance)
			self::$instance = new self;
		return self::$instance;
	}

	protected static $instance;
	protected static $migrated = false;





	protected $pdo;

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
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET SQL_MODE='TRADITIONAL', TIME_ZONE='{$timezone}';",
			]
		);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

		// Get cache
		$this->cache = new Cache(__CLASS__, null);

		// Migrate if we haven't
		if( ! self::$migrated)
			$this->migrate();

		// Preload cache
		$this->cache->preload([$this, 'loadTableInfo']);
	}



	const DIR = __DIR__.DIRECTORY_SEPARATOR.'_schema'.DIRECTORY_SEPARATOR;

	public function migrate()
	{
		// Try get version number
		try
		{
			$q = $this->pdo->query('SELECT * FROM version');
			$current = (int) $q->fetchColumn(0);
			$q->closeCursor();
		}
		catch(PDOException $e)
		{
			// Create version table
			$this->pdo->exec('CREATE TABLE IF NOT EXISTS `version` (`version` int(10) UNSIGNED NOT NULL) ENGINE=InnoDB');
			$this->pdo->exec('INSERT INTO `version` VALUES(0)');
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
					$script = preg_replace('/#.++/', NULL, file_get_contents($m));

					// Split into queries
					$queries = preg_split('/;\s*$/m', $script, -1, PREG_SPLIT_NO_EMPTY);

					// Run each query
					foreach($queries as $q)
						if(trim($q) != '')
							$this->pdo->exec($q);

					// Run <version>.php if it exists
					if(file_exists(self::DIR.$version.'.php'))
						require self::DIR.$version.'.php';

					// Update version table
					$this->pdo->exec('UPDATE version SET version = '.$version);

					// Clear DB cache
					$this->cache->clear();
				}
				catch(PDOException $e)
				{
					throw new HttpException('DB Migration failed.', 500, $e);
				}
			}
		}

		self::$migrated = true;
	}


	public function loadTableInfo()
	{
		$tables = $this->pdo
			->query('SHOW TABLES')
			->fetchAll(PDO::FETCH_COLUMN, 0);

		foreach($tables as $table)
		{
			$columns = $this->pdo
				->query("SHOW COLUMNS FROM $table")
				->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);
			$columns = array_map('reset', $columns);

			$info = (object)[
				'columns' => $columns,
				'column_names' => array_keys($columns), 
				'primary_keys' => [],
				'rules' => [],
				'auto_increment' => false,
				];

			foreach ($columns as $name => $column)
			{
				// If auto_increment
				if($column['Extra'] == 'auto_increment')
					// Remember name for lastInsertId on save
					$info->auto_increment = $name;

				// If not, and not nullable
				elseif($column['Null'] == 'NO')
					// Add not_empty rule
					$info->rules[$name][] = 'not_empty';

				// Add db_type rule
				$info->rules[$name][] = ['db_type', $column['Type']];
			}

			yield $table => $info;
		}
	}
}
