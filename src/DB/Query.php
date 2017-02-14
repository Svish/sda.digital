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
		$this->statement->execute($input_parameters);
		$this->close();
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
	public function fetchAll($fetch_argument = 'stdClass', ...$ctor_arguments)
	{
		return $this->statement->fetchAll(PDO::FETCH_CLASS, $fetch_argument, $ctor_arguments);
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
	public function fetchFirst($class_name = null, array $ctor_arguments = [])
	{
		$result = $this->fetch($class_name, $ctor_arguments);
		$this->close();
		return $result;
	}

	/**
	 * Fetches given column from first row and closes the cursor.
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
}
