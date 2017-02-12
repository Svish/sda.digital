<?php

use DB\Query;
use Cache\PreCheckedCache;


/**
 * PDO helper
 *
 * @see https://phpdelusions.net/pdo
 */
class DB
{
	const MIGRATIONS_DIR = __DIR__.DIRECTORY_SEPARATOR.'_schema'.DIRECTORY_SEPARATOR;



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
	
	public static function getTableInfo(string $table_name)
	{
		return self::instance()->cache->get($table_name, function($key)
			{
				throw new Exception("DB::getTableInfo('$key') missing");
			});
	}

	public static function instance()
	{
		if (!self::$instance)
			self::$instance = new self;
		return self::$instance;
	}

	protected static $instance;
	protected static $migrate = true;



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
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET
					SQL_MODE='TRADITIONAL',
					TIME_ZONE='{$timezone}'
					;",
			]
		);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

		// Get cache
		class_exists('DB\\Query');
		class_exists('DB\\Valid');
		$this->cache = new PreCheckedCache(__CLASS__, [$this, 'loadTableInfo']);

		// Migrate if we haven't
		if(self::$migrate)
		{
			$this->migrate();
			self::$migrate = false;
		}
	}




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
			$this->pdo->exec('CREATE TABLE IF NOT EXISTS `version` (
				`version` int(10) UNSIGNED NOT NULL
				) ENGINE=InnoDB');
			$this->pdo->exec('INSERT INTO `version` VALUES(0)');
			$current = 0;
		}

		// Get migration files
		$files = glob(self::MIGRATIONS_DIR.'*.sql', GLOB_NOSORT);
		natsort($files);

		// Sort out any not needed
		foreach($files as $key => &$f)
		{
			$version = (int) str_replace(self::MIGRATIONS_DIR, NULL, $f);
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

		if($files)
		{
			// Disable key checks while migrating
			$this->pdo->exec("SET foreign_key_checks = 0");

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
						$this->pdo->exec($q);

					// Run <version>.php if it exists
					if(file_exists(self::MIGRATIONS_DIR.$version.'.php'))
						require self::MIGRATIONS_DIR.$version.'.php';

					// Update version table
					$this->pdo->exec('UPDATE version SET version = '.$version);
				}
				catch(PDOException $e)
				{
					throw new Exception('DB Migration failed.', 0, $e);
				}
			}

			// Re-enable key checks
			$this->pdo->exec("SET foreign_key_checks = 1");

			// Reload DB cache
			$this->cache->reload();

			Message::ok('db-migrated', $version);
		}
	}


	public function loadTableInfo()
	{
		// Get database name
		$db_name = $this->pdo
			->query("SELECT database()")
			->fetchColumn(0);

		// Get table names
		$tables = $this->pdo
			->query('SHOW TABLES')
			->fetchAll(PDO::FETCH_COLUMN, 0);

		// Get relations
		$relations = $this->pdo
			->query("SELECT 
						table_name,
						column_name,
						referenced_table_schema,
						referenced_table_name,
						referenced_column_name
					FROM
						information_schema.key_column_usage
					WHERE
  							table_schema = '$db_name'
						AND referenced_table_name IS NOT NULL
					")
			->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);

		// TODO: Analyze relations

		// Gather table info
		foreach($tables as $table)
		{
			// Fetch column info
			$columns = $this->pdo
				->query("SHOW COLUMNS FROM $table")
				->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);

			$info = (object)[
				'columns' => $columns,
				'column_names' => array_keys($columns), 
				'column_pdo_types' => [],
				'rules' => [],
				'auto_inc_column' => false,
				];

			// Add more info
			foreach ($columns as $name => $column)
			{
				// If auto_increment
				if($column['Extra'] == 'auto_increment')
					// Remember name for lastInsertId on save
					$info->auto_inc_column = $name;

				// If not, and not nullable
				elseif($column['Null'] == 'NO')
					// Add not_empty rule
					$info->rules[$name][] = 'not_empty';

				// Add db type rule
				$info->rules[$name][] = [['DB\\Valid', 'type'], $column['Type']];

				// TODO: Add unique rule
				//if($column['Key'] == 'UNI')
					//$info->rules[$name][]

				// Add pdo type
				$info->column_pdo_types[$name] = self::pdo_type($column['Type']);
			}

			yield $table => $info;
		}
	}

	private static function pdo_type($type)
	{
		switch(preg_replace('/\(.+/', null, $type))
		{
			case 'tinyint':
			case 'smallint':
			case 'mediumint':
			case 'int':
			case 'bigint':
				return PDO::PARAM_INT;

			default:
				return PDO::PARAM_STR;
		}
	}
}
