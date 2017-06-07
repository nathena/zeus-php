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
    private $insertSqlFormat = "INSERT INTO `%s` ( %s ) VALUES ( %s )";

    public function __construct($table)
    {
        parent::__construct(DmlType::DML_INSERT);
        $this->table = trim($table);
    }

    public function prepare(array $params){
        if( empty($params)){
            return;
        }

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

    public function prepareBatch(array $params){
        if (count($params) < 1){
            return;
        }

        $_params = $params[0];
        if(empty($_params)){
            return;
        }

        $insert_key = $insert_value = $comma = "";
        foreach($_params as $field => $val )
        {
            $insert_key .= $comma . '`' .$field . '`';
            $insert_value .= $comma.":".$field;
            $comma = ",";
        }

        $_batch_params = [];
        foreach($params as $_params){
            if(!is_array($_params)){
                return;
            }
            $_batch_param = [];
            foreach($_params as $field => $val )
            {
                $_batch_param[":".$field] = $val;
            }
            $_batch_params[] = $_batch_param;
        }

        $this->params = $_batch_params;
        $this->sql = sprintf($this->insertSqlFormat,$this->table,$insert_key,$insert_value);
    }
}