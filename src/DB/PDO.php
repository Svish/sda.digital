<?php

namespace DB;

/**
 * PDO extensions.
 *
 * @see http://php.net/manual/en/pdo.begintransaction.php#116669
 */
class PDO extends \PDO
{
	private $transactionCount = 0;


	public function beginTransaction()
	{
		if ( ! $this->transactionCounter++)
			return parent::beginTransaction();

		$this->exec('SAVEPOINT trans'.$this->transactionCounter);
		return $this->transactionCounter >= 0;
	}


	public function commit()
	{
		if ( ! --$this->transactionCounter)
			return parent::commit();
		
		return $this->transactionCounter >= 0;
	}
	

	public function rollback()
	{
		if(--$this->transactionCounter)
		{
			$this->exec('ROLLBACK TO trans'.$this->transactionCounter + 1);
			return true;
		}
		return parent::rollback();
	}
}
