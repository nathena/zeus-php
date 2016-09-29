<?php
namespace zeus\store\db\pdo;

/**
 * 
 * @author nathena
 *
 */

class Pdo extends GeneralPdo
{
    protected $transactionCount = 0;

    public function __construct( $cfg )
    {
        parent::__construct($cfg);
    }

    public function beginTransaction()
    {
    	if( $this->transactionCounter == 0 )
    	{
    		return $this->pdo->beginTransaction();
    	}
    	
    	$this->pdo->exec('SAVEPOINT transid_'.$this->transactionCounter);
    	
    	return $this->transactionCounter++;
    }

    public function commit()
    {
    	if( $this->transactionCounter == 0 )
    	{
    		 return $this->pdo->commit();
    	}
    	
    	return $this->transactionCounter--;
    }

    public function rollBack()
    {
    	if( $this->transactionCounter == 0 )
    	{
    		return $this->pdo->rollBack();
    	}
    	
    	$this->pdo->exec('ROLLBACK TO transid_'.$this->transactionCounter);
    	
    	return $this->transactionCounter--;
    }
}