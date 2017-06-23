<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/7 0007
 * Time: 10:46
 */

namespace zeus\database\specification;


use zeus\database\DmlType;

abstract class AbstractSpecification
{
    protected $params = [];
    protected $sql = "";

    protected $dml;

    public function getSql()
    {
        return $this->sql;
    }

    public function setSql($sql)
    {
        $this->sql = $sql;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParams(array $params)
    {
        $this->params = array_merge($this->params, $params);
    }

    public function getDml()
    {
        return $this->dml;
    }

    public function log()
    {
        $sql = $this->getSql();
        if (!is_array($sql)) {
            $sql = [$sql];
        }
        $param = $this->getParams();

        $sql_s = [];
        foreach ($sql as $_sql) {
            if (DmlType::DML_BATCH == $this->dml) {
                foreach ($param as $item) {
                    $sql_s[] = $this->_real_sql($_sql, $item);
                }
            } else {
                $sql_s[] = $this->_real_sql($_sql, $param);
            }
        }
        echo "\r\n" . implode(",", $sql_s) . "\r\n";
    }

    private function _real_sql($sql, $param)
    {
        $indexed = $param == array_values($param);

        foreach ($param as $k => $v) {
            if (is_string($v)) {
                $v = "'$v'";
            }
            if ($indexed) {
                $sql = preg_replace('/\?/', $v, $sql, 1);
            } else {
                $sql = preg_replace("/$k/", $v, $sql, 1);
            }
        }
        return $sql;
    }
}