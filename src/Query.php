<?php

/**
 * A simple PDOStatement wrapper for method chaining 
 * and streamlining of common defaults.
 */
class Query
{
	// TODO: Document methods.

	private $pdo;

	public function __construct(PDOStatement $statement)
	{
		$this->pdo = $statement;
	}

	public function bindParam($parameter, &$variable, $data_type = PDO::PARAM_STR)
	{
		$this->pdo->bindParam($parameter, $variable, $data_type);
		return $this;
	}

	public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
	{
		$this->pdo->bindParam($parameter, $value, $data_type);
		return $this;
	}

	public function execute($input_parameters = NULL)
	{
		$this->pdo->execute($input_parameters);
		return $this;
	}

	public function close()
	{
		$this->pdo->closeCursor();
	}

	public function exec($input_parameters = NULL)
	{
		$this->pdo->execute($input_parameters);
		$this->close();
	}

	public function lastInsertId()
	{
		return DB::instance()->lastInsertId();
	}

	public function rowCount()
	{
		return $this->pdo->rowCount();
	}

	public function fetch($class_name = NULL, $ctor_arguments = array())
	{
		$result = $class_name
			? $this->pdo->fetchObject($class_name, $ctor_arguments)
			: $this->pdo->fetch(PDO::FETCH_ASSOC);
		$this->close();
		return $result;

	}

	public function fetchArray($grouped = false)
	{
		return $grouped
			? $this->pdo->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP)
			: $this->pdo->fetchAll(PDO::FETCH_ASSOC);
	}

	public function fetchAll($fetch_argument = 'stdClass', $ctor_arguments = NULL, $fetch_style = PDO::FETCH_CLASS)
	{
		return $this->pdo->fetchAll($fetch_style, $fetch_argument, $ctor_arguments);
	}

	public function fetchAllColumn($column = 0)
	{
		return $this->pdo->fetchAll(PDO::FETCH_COLUMN, $column);
	}

	public function fetchColumn($column = 0)
	{
		$result = $this->pdo->fetchColumn($column);
		$this->close();
		return $result;
	}

	public function debug()
	{
		$this->pdo->debugDumpParams();
		return $this;
	}
}
