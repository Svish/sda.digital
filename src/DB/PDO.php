<?php

namespace DB;

/**
 * PDO extensions.
 *
 * @see http://php.net/manual/en/pdo.begintransaction.php#116669
 */
class PDO extends \PDO
{
	private $_counter = 0;


	public function beginTransaction()
	{
		if ( ! $this->_counter++)
			return parent::beginTransaction();

		$this->exec('SAVEPOINT trans'.$this->_counter);
		return $this->_counter >= 0;
	}


	public function commit()
	{
		if ( ! --$this->_counter)
			return parent::commit();
		
		return $this->_counter >= 0;
	}
	

	public function rollback()
	{
		if(--$this->_counter)
		{
			$this->exec('ROLLBACK TO trans'.($this->_counter+1));
			return true;
		}
		return parent::rollback();
	}
}
