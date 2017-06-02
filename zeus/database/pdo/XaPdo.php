<?php
namespace zeus\database\pdo;
use zeus\base\ApplicationContext;

/**
 * 
 * @author nathena
 *
 */
class XaPdo extends AbstractPdoDialect
{
    private $xid;

    protected $xaCount = 0;
    
    public function __construct($cfg)
    {
        parent::__construct($cfg);

        $this->xid = XaIdGenerator::getXaId();
    }

    public function beginTransaction()
    {
    	if($this->xaCount == 0)
    	{
    		return $this->pdo->exec("XA START ".$this->xid);
    	}
    	
    	return $this->xaCount++;
    }
    
    public function prepare()
    {
        return $this->pdo->exec("XA END ".$this->xid);
    	return $this->pdo->exec("XA PREPARE ".$this->xid);
    }
    
    public function commit()
    {
    	if($this->xaCount == 0)
    	{
    		return $this->pdo->exec("XA COMMIT ".$this->xid);
    	}
    	return $this->xaCount--;
    }
    
    public function rollBack()
    {
    	if($this->xaCount == 0)
    	{
    		return $this->pdo->exec("XA ROLLBACK ".$this->xid);
    	}
    	return $this->xaCount--;
    }
}