<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/7 0007
 * Time: 10:42
 */

namespace zeus\database\specification;

use zeus\database\DmlType;

class InsertSpecification extends AbstractSpecification
{
    private $table;
    private $fields = [];
    private $insertSqlFormat = "INSERT INTO `%s` ( %s ) VALUES ( %s )";

    public function __construct($table,$fields)
    {
        $this->table = trim($table);
        $this->dml = DmlType::DML_INSERT;
        $this->fields = array_merge($this->fields,$fields);
    }

    public function getSql()
    {
        $this->init();

        return parent::getSql();
    }

    protected function init(){

        $params = $this->fields;

        $insert_key = $insert_value = $comma = "";
        foreach($params as $field => $val )
        {
            $insert_key .= $comma . '`' .$field . '`';
            $insert_value .= $comma.":".$field;
            $comma = ",";

            $this->params[":".$field] = $val;
        }

        $this->sql = sprintf($this->insertSqlFormat,$this->table,$insert_key,$insert_value);
    }
}