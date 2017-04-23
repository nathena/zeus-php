<?php
namespace zeus\ddd\infrastructure\persistent\pdo;

/**
 * 
 * @author nathena
 *
 */

class Pdo extends AbstractPdoDialect
{
    protected $transactionCounter = 0;

    public function __construct( $cfg )
    {
        parent::__construct($cfg);
    }

    public function beginTransaction($nested=false)
    {
    	if( $this->transactionCounter == 0 )
    	{
    		return $this->pdo->beginTransaction();
    	}
    	
    	if( $nested )
    	{
    		$this->pdo->exec('SAVEPOINT transid_'.$this->transactionCounter);
    	}
    	
    	return $this->transactionCounter++;
    }

    public function commit($nested=false)
    {
    	if( $this->transactionCounter == 0 )
    	{
    		 return $this->pdo->commit();
    	}
    	
    	return $this->transactionCounter--;
    }

    public function rollBack($nested=false)
    {
    	if( $this->transactionCounter == 0 )
    	{
    		return $this->pdo->rollBack();
    	}
    	
    	if( $nested )
    	{
    		$this->pdo->exec('ROLLBACK TO transid_'.$this->transactionCounter);
    	}
    	
    	return $this->transactionCounter--;
    }
}