<?php
namespace zeus\store\db\pdo;

/**
 * 
 * @author nathena
 *
 */

abstract class AbstractPdoDialect
{
	private $insertSqlFormat = "INSERT INTO `%s` ( %s ) VALUES ( %s )";
	private $insertsSqlFormat = "INSERT INTO `%s` ( %s ) VALUES  %s ";
	private $updateSqlFormat = "UPDATE `%s` set %s %s ";
	private $deleteSqlFormat = "DELETE FROM `%s` %s ";
	
	protected $pdo;
	
	public $sql = array();
	public $param = array();
	
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
		$charset = isset($cfg["charset"]) ? trim($cfg["charset"]) : "utf8";
		
		$driver_options = isset($cfg["driver_options"]) ? trim($cfg["driver_options"]) : null;
	
		$this->pdo = new \PDO($dsn, $user, $pass, $driver_options);
		$this->pdo->exec("SET NAMES $charset");
	
		//取保连接关闭
		register_shutdown_function(array($this, 'close'));
	}
	
	public function close()
	{
		$this->pdo = null;
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
	
	
	
	public function beginTransaction($nested=false)
	{
		
	}
	
	public function commit($nested=false)
	{
		
	}
	
	public function rollBack($nested=false)
	{
		
	}
}