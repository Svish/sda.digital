<?php

namespace DB;
use PDOStatement, PDO;

/**
 * PDOStatement wrapper with common defaults
 *  and method chaining.
 */
class Query
{
	protected $statement;
	protected $pdo;

	public function __construct(PDOStatement $statement, PDO $pdo)
	{
		$this->statement = $statement;
		$this->pdo = $pdo;
	}



	/**
	 * Binds a parameter as a reference.
	 *
	 * @return $this
	 */
	public function bindParam($parameter, &$variable, $data_type = PDO::PARAM_STR): self
	{
		$this->statement->bindParam($parameter, $variable, $data_type);
		return $this;
	}

	/**
	 * Binds a parameter as a value.
	 *
	 * @return $this
	 */
	public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR): self
	{
		$this->statement->bindParam($parameter, $value, $data_type);
		return $this;
	}



	/**
	 * Executes the query.
	 *
	 * @return $this.
	 */
	public function execute($input_parameters = null): self
	{
		$this->statement->execute($input_parameters);
		return $this;
	}

	/**
	 * Executes the query and closes the cursor.
	 *
	 * @return number of affected rows.
	 */
	public function exec($input_parameters = null): int
	{
		try
		{
			$this->statement->execute($input_parameters);

		}
		catch(\PDOException $e)
		{
			// https://mariadb.com/kb/en/mariadb/mariadb-error-codes/
			switch($e->errorInfo[1])
			{
				case 1062: // ER_DUP_ENTRY
					throw new \Error\Duplicate($e);
				case 1216: // ER_NO_REFERENCED_ROW
				case 1217: // ER_ROW_IS_REFERENCED
				case 1451: // ER_ROW_IS_REFERENCED_2
				case 1452: // ER_NO_REFERENCED_ROW_2
					throw new \Error\KeyConstraint($e);
					
				default:
					throw $e;
			}
		}
		finally
		{
			$this->close();
		}
		return $this->affectedRows();
	}



	/**
	 * Closes the cursor.
	 */
	public function close(): self
	{
		$this->statement->closeCursor();
		return $this;
	}



	/**
	 * @return Last insert id from DB::instance().
	 */
	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
	}

	/**
	 * @return Row count query.
	 */
	public function affectedRows(): int
	{
		return $this->statement->rowCount();
	}



	/**
	 * Fetches the first row.
	 */
	public function fetch($class_name = null, array $ctor_arguments = [])
	{
		$result = $class_name
			? $this->statement->fetchObject($class_name, $ctor_arguments)
			: $this->statement->fetch(PDO::FETCH_ASSOC);
		return $result;

	}

	/**
	 * Fetches all rows as assoc array.
	 */
	public function fetchArray($grouped = false)
	{
		return $grouped
			? $this->statement->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP)
			: $this->statement->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Fetches all rows.
	 */
	public function fetchAll(string $class = 'stdClass', ...$ctor_arguments)
	{
		return $this->statement->fetchAll(PDO::FETCH_CLASS, $class, $ctor_arguments);
	}

	/**
	 * Fetches all values of given column.
	 */
	public function fetchAllColumn($column = 0)
	{
		return $this->statement->fetchAll(PDO::FETCH_COLUMN, $column);
	}





	/**
	 * Fetches the first row, and closes the cursor.
	 */
	public function fetchFirst($class_name = 'stdClass', ...$ctor_arguments)
	{
		$result = $this->fetch($class_name, $ctor_arguments);
		$this->close();
		return $result;
	}

	/**
	 * Fetches first rows as assoc array, and closes the cursor
	 */
	public function fetchFirstArray()
	{
		$result = $this->statement->fetch(PDO::FETCH_ASSOC);
		$this->close();
		return $result;
	}

	/**
	 * Fetches given column from first row, and closes the cursor.
	 */
	public function fetchFirstColumn($column = 0)
	{
		$result = $this->statement->fetchColumn($column);
		$this->close();
		return $result;
	}





	/**
	 * Dumps debug parameters.
	 *
	 * @return $this
	 */
	public function debug()
	{
		$this->statement->debugDumpParams();
		return $this;
	}





	/**
	 * Fetch yielding
	 */
	public function stream()
	{
		// TODO: fetch and yield
	}

	/**
	 * Fetch
	 */
	public function streamRelationships()
	{
		// TODO: fetch and yield
		// Set relationships somehow
	}
}
