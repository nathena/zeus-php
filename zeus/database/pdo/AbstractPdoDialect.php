<?php

namespace zeus\database\pdo;

use zeus\base\logger\Logger;
use zeus\database\DmlType;
use zeus\database\specification\AbstractSpecification;

/**
 *
 * @author nathena
 *
 */
abstract class AbstractPdoDialect
{
    protected $pdo;
    protected $benchmark = 0;
    protected $sql = [];

    private $deleteSqlFormat = "DELETE FROM `%s` %s ";

    /**
     * @param $cfg
     * mysql:host=localhost;port=3306;dbname=testdb
     * mysql:unix_socket=/tmp/mysql.sock;dbname=testdb
     * array(
     * "dsn"=>"",
     * "user"=>"",
     * "pass"=>"",
     * )
     */
    public function __construct($cfg)
    {
        $dsn = $cfg["dsn"];
        $user = isset($cfg["user"]) ? trim($cfg["user"]) : "";
        $pass = isset($cfg["pass"]) ? trim($cfg["pass"]) : "";
        $charset = isset($cfg["charset"]) ? trim($cfg["charset"]) : "utf8";

        /**
         * 'database.pdo.driver_options' => [
         * \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
         * \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
         * ],
         */
        $driver_options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
        if (strpos($dsn, 'mysql') !== FALSE) {
            $driver_options[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$charset}";
        }

        try {
            $this->pdo = new \PDO($dsn, $user, $pass, $driver_options);
            //取保连接关闭
            register_shutdown_function(array($this, 'close'));
        } catch (\PDOException $e) {
            Logger::error(">> PDO connect Error: " . $e->getCode() . "," . implode(",", array_values($e->errorInfo)));
            throw $e;
        }
    }

    public function close()
    {
        $this->pdo = null;
    }

    public function beginTransaction($nested = false)
    {

    }

    public function commit($nested = false)
    {

    }

    public function rollBack($nested = false)
    {

    }

    /**
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    public function debug()
    {
        return [$this->benchmark, $this->sql];
    }

    public function execute(AbstractSpecification $specification)
    {
        $prepare = $specification->getSql();
        $params = $specification->getParams();
        $dml = $specification->getDml();

        if (DmlType::DML_INSERT_BATCH == $dml) {
            return $this->_execute_batch($prepare, $params);
        } else {
            return $this->_execute($prepare, $params, $dml);
        }
    }

    protected function _execute($prepare, $params, $dml)
    {
        $_sql = $this->log($prepare, $params);
        list($sm, $ss) = explode(' ', microtime());

        try {
            $sth = $this->pdo->prepare($prepare);
            $sth->execute($params);
            switch ($dml) {
                case DmlType::DML_SELECT_ONE:
                    $result = $sth->fetch();
                    break;
                case DmlType::DML_SELECT_LIST:
                    $result = $sth->fetchAll();
                    break;
                case DmlType::DML_INSERT:
                    $result = $this->pdo->lastInsertId();
                    break;
                case DmlType::DML_UPDATE:
                    $result = $sth->rowCount();
                    break;
                case DmlType::DML_DELETE:
                    $result = $sth->rowCount();
                    break;
                default:
                    $result = $sth->rowCount();
                    break;
            }

            list($em, $es) = explode(' ', microtime());
            $benchmark = ($em + $es) - ($sm + $ss);
            $this->benchmark += $benchmark;
            $this->sql[$benchmark] = $_sql;

            return $result;
        } catch (\PDOException $e) {
            Logger::error(">> Query Error: " . $e->getCode() . "," . implode(",", array_values($e->errorInfo)) . " - {$_sql}");
            throw $e;
        }
    }

    protected function _execute_batch($prepare, $params)
    {
        if (count($params) < 1) {
            return;
        }

        $_sql = $this->log($prepare, $params, true);

        list($sm, $ss) = explode(' ', microtime());

        try {
            $sth = $this->pdo->prepare($prepare);
            foreach ($params as $_params) {
                $sth->execute($_params);
            }
            $result = $sth->rowCount();

            list($em, $es) = explode(' ', microtime());
            $benchmark = ($em + $es) - ($sm + $ss);
            $this->benchmark += $benchmark;
            $this->sql[$benchmark] = $_sql;

            return $result;
        } catch (\PDOException $e) {
            Logger::error(">> Query Error: " . $e->getCode() . "," . implode(",", array_values($e->errorInfo)) . " - {$_sql}");
            throw $e;
        }
    }

    private function log($sql, $param, $is_batch = false)
    {
        $sql_s = [];
        if ($is_batch) {
            foreach ($param as $item) {
                $sql_s[] = $this->_real_sql($sql, $item);
            }
        } else {
            $sql_s[] = $this->_real_sql($sql, $param);
        }
        return implode(",", $sql_s);
    }

    private function _real_sql($sql, $param)
    {
        $indexed = $param == array_values($param);

        foreach ($param as $k => $v) {
            if (is_string($v)) {
                $v = "'$v'";
            }
            if ($indexed) {
                $sql = preg_replace('/\?/', $this->pdo->quote($v), $sql, 1);
            } else {
                $sql = preg_replace("/$k/", $this->pdo->quote($v), $sql, 1);
            }
        }
        return $sql;
    }
}