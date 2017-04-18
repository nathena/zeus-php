<?php
namespace zeus\foundation\store\pdo;

/**
 * 
 * @author nathena
 *
 */
class XaPdo extends AbstractPdoDialect
{
    private $xid;

    protected $xaCount = 0;
    
    public function __construct( $cfg ,$xid )
    {
        parent::__construct($cfg);
        
        $this->xid = $xid;
    }

    public function start()
    {
    	if($this->xaCount == 0)
    	{
    		return $this->pdo->exec("XA START ".$this->xid);
    	}
    	
    	return $this->xaCount++;
    }
    
    public function end()
    {
    	return $this->pdo->exec("XA END ".$this->xid);
    }
    
    public function prepare()
    {
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