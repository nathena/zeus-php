<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/7 0007
 * Time: 10:42
 */

namespace zeus\database\specification;


class InsertSpecification extends AbstractSpecification
{
    private $table;
    private $insertSqlFormat = "INSERT INTO `%s` ( %s ) VALUES ( %s )";

    public function __construct($table,$params)
    {
        parent::__construct("insert");

        $this->table = trim($table);
        $this->prepare($params);
    }

    private function prepare($params){
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