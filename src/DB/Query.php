<?php

namespace DB;
use PDOStatement, PDO;

/**
 * PDOStatement wrapper with common defaults
 * and method chaining.
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
	public function bindParam($parameter, &$variable, $data_type = PDO::PARAM_STR)
	{
		$this->statement->bindParam($parameter, $variable, $data_type);
		return $this;
	}

	/**
	 * Binds a parameter as a value.
	 *
	 * @return $this
	 */
	public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
	{
		$this->statement->bindParam($parameter, $value, $data_type);
		return $this;
	}



	/**
	 * Executes the query.
	 *
	 * @return $this.
	 */
	public function execute($input_parameters = NULL)
	{
		$this->statement->execute($input_parameters);
		return $this;
	}

	/**
	 * Executes the query and closes the cursor.
	 *
	 * @return true on success; false otherwise.
	 */
	public function exec($input_parameters = NULL)
	{
		$result = $this->statement->execute($input_parameters);
		$this->close();
		return $result;
	}



	/**
	 * Closes the cursor.
	 */
	public function close()
	{
		$this->statement->closeCursor();
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
	public function affectedRows()
	{
		return $this->statement->rowCount();
	}



	/**
	 * Fetches the first row, and closes the cursor.
	 */
	public function fetchFirst($class_name = NULL, array $ctor_arguments = [])
	{
		$result = $this->fetch($class_name, $ctor_arguments);
		$this->close();
		return $result;
	}

	/**
	 * Fetches the first row.
	 */
	public function fetch($class_name = NULL, array $ctor_arguments = [])
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
	public function fetchAll($fetch_argument = 'stdClass', $ctor_arguments = NULL, $fetch_style = PDO::FETCH_CLASS)
	{
		return $this->statement->fetchAll($fetch_style, $fetch_argument, $ctor_arguments);
	}

	/**
	 * Fetches all values of given column.
	 */
	public function fetchAllColumn($column = 0)
	{
		return $this->statement->fetchAll(PDO::FETCH_COLUMN, $column);
	}

	/**
	 * Fetches given column from first row and closes the cursor.
	 */
	public function fetchColumn($column = 0)
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
