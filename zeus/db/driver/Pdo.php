<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 15/8/5
 * Time: 17:03
 */

namespace zeus\db\driver;


class Pdo
{
    private $xid;

    protected $pdo;
    protected $transactionCount = 0;

    public $sql = array();
    public $param = array();

    private $insertSqlFormat = "INSERT INTO `%s` ( %s ) VALUES ( %s )";
    private $insertsSqlFormat = "INSERT INTO `%s` ( %s ) VALUES  %s ";
    private $updateSqlFormat = "update `%s` set %s %s ";
    private $deleteSqlFormat = "DELETE FROM `%s` %s ";

    /**
     * @param $cfg
     *
     * array(
        "dsn"=>"",
        "user"=>"",
        "pass"=>"",
        "driver_options"=>null
        )
     */
    public function __construct( $cfg )
    {
        $dsn = $cfg["dsn"];
        $user = isset($cfg["user"]) ? trim($cfg["user"]) : "";
        $pass = isset($cfg["pass"]) ? trim($cfg["pass"]) : "";

        $driver_options = isset($cfg["driver_options"]) ? trim($cfg["driver_options"]) : null;

        $this->pdo = new \PDO($dsn, $user, $pass, $driver_options);
        $this->pdo->exec("SET NAMES utf8mb4");

        //取保连接关闭
        register_shutdown_function(array($this, 'close'));
    }

    public function close()
    {
        $this->pdo = null;
    }
    
    public function xid($xid)
    {
    	$this->xid = $xid;
    }

    public function queryForValue($prepare,$params=null,$index=0)
    {
        $sth = $this->pdo->prepare($prepare);
        $sth->execute($params);

        $this->sql[] = $prepare;
        $this->param[] = $params;

        $result = $sth->fetch();

        return (!empty($result) && is_array($result)) ? $result[$index] : null;
    }

    public function query($prepare,$params=null)
    {
        $sth = $this->pdo->prepare($prepare);
        $sth->execute($params);

        $this->sql[] = $prepare;
        $this->param[] = $params;

        return $sth->fetch();
    }

    public function queryForList($prepare,$params=null)
    {
        $sth = $this->pdo->prepare($prepare);
        $sth->execute($params);

        $this->sql[] = $prepare;
        $this->param[] = $params;

        return $sth->fetchAll();
    }

    public function exec($prepare,$params=null)
    {
        $sth = $this->pdo->prepare($prepare);
        $sth->execute($params);

        $this->sql[] = $prepare;
        $this->param[] = $params;
    }

    public function insert($table,$fields)
    {
        $insertkeysql = $insertvaluesql = $comma = "";
        $params = array();

        foreach($fields as $field => $val )
        {
            $insertkeysql .= $comma . '`' .$field . '`';
            $insertvaluesql .= $comma.":".$field;

            $comma = ",";

            $params[":".$field] = $val;
        }

        $sql = sprintf($this->insertSqlFormat,$table,$insertkeysql,$insertvaluesql);

        $sth = $this->pdo->prepare($sql);
        $sth->execute($params);

        $this->sql[] = $sql;
        $this->param[] = $params;

        return $this->pdo->lastInsertId();
    }

    public function update($table,$fields,$wheresql)
    {
        $values = array();
        $setsql = $comma = "";

        foreach($fields as $fields => $val )
        {
            $setsql .= $comma . '`' .$fields. '` = ? ';
            $comma = ",";

            $values[] = $val;
        }

        $_where = $comma = '';
        if( is_array($wheresql) )
        {
            foreach($wheresql as $key => $val )
            {
                $_where .= $comma . '`' .$key. '` = ? ';
                $comma = " and ";

                $values[] = $val;
            }
        }
        else
        {
            $_where = $wheresql;
        }

        if( $_where )
        {
            $_where = " where ".$_where;
        }

        $sql = sprintf($this->updateSqlFormat,$table,$setsql,$_where);

        $sth = $this->pdo->prepare($sql);
        $sth->execute($values);

        $this->sql[] = $sql;
        $this->param[] = $values;

        return $sth->rowCount();
    }

    public function delete($table,$wheresql)
    {
        $values = array();
        $_where = $comma = '';

        if( is_array($wheresql) )
        {
            foreach($wheresql as $key => $val )
            {
                $_where .= $comma . '`' .$key. '` = ? ';
                $comma = " and ";

                $values[] = $val;
            }
        }
        else
        {
            $_where = $wheresql;
        }

        if( $_where )
        {
            $_where = " where ".$_where;
        }

        $sql = sprintf($this->deleteSqlFormat,$table,$_where);

        $sth = $this->pdo->prepare($sql);
        $sth->execute($values);

        $this->sql[] = $sql;
        $this->param[] = $values;

        return $sth->rowCount();
    }

    public function inserts($table,$fieldsArr)
    {
        if (count($fieldsArr) < 1)
        {
            return 0;
        }

        $values = array();
        $insertkeysql = $insertvaluesql = $comma = $out = $in = '';

        $keys = array_keys(current($fieldsArr));
        foreach ($keys as $insert_key) {
            $insertkeysql .= $comma . '`' . $insert_key . '`';
            $comma = ', ';
        }

        foreach ($fieldsArr as $fields) {
            $in = '';
            $insertvaluesql.=$out . '(';
            foreach ($fields as $insert_value) {

                $insertvaluesql .= $in."?";
                $in = ',';

                $values[] = $insert_value;
            }
            $insertvaluesql.=')';
            $out = ',';
        }

        $sql = sprintf($this->insertsSqlFormat,$table,$insertkeysql,$insertvaluesql);

        $sth = $this->pdo->prepare($sql);
        $sth->execute($values);

        $this->sql[] = $sql;
        $this->param[] = $values;

        return $sth->rowCount();
    }

    public function beginTransaction()
    {
        if(!$this->transactionCounter++)
        {
        	if( isset($this->xid) )
        	{
        		return $this->pdo->exec("XA START ".$this->xid);
        	}
        	else 
        	{
        		return $this->pdo->beginTransaction();
        	}
        }

        $this->pdo->exec('SAVEPOINT trans '.$this->transactionCounter);
        
        return $this->transactionCounter >= 0;
    }

    public function commit()
    {
        if (!--$this->transactionCounter)
        {
            if( isset($this->xid) )
            {
            	return $this->pdo->exec("XA COMMIT ".$this->xid);
            }
            else
            {
            	return $this->pdo->commit();
            }
        }
        return $this->transactionCounter >= 0;
    }

    public function rollBack()
    {
        if (--$this->transactionCounter)
        {
            return $this->pdo->exec('ROLLBACK TO trans '.$this->transactionCounter + 1);
        }
        
        if( isset($this->xid) )
        {
        	return $this->pdo->exec("XA ROLLBACK ".$this->xid);
        }
        else
        {
        	return $this->pdo->rollBack();
        }
    }

    public function xaEnd()
    {
        if( isset($this->xid) )
        {
        	$this->pdo->exec("XA END ".$this->xid);
        }
    }
    
    public function xaPrepare()
    {
    	if( isset($this->xid) )
    	{
    		$this->pdo->exec("XA PREPARE ".$this->xid);
    	}
    }
}