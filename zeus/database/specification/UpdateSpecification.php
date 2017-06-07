<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/7 0007
 * Time: 10:43
 */

namespace zeus\database\specification;

use zeus\database\DmlType;

class UpdateSpecification extends AbstractWhereSpecification
{
    private $table;
    private $updateSqlFormat = "UPDATE `%s` set %s %s ";

    public function __construct($table)
    {
        parent::__construct(DmlType::DML_UPDATE);
        $this->table = $table;
    }

    public function update(array $params){
        if( empty($params)){
            return;
        }

        $set_sql = $comma = "";
        foreach($params as $fields => $val )
        {
            $set_sql .= "{$comma} `{$fields}` = :{$fields}";
            $comma = ",";

            $this->params[":{$fields}"] = $val;
        }

        $this->sql = sprintf($this->updateSqlFormat,$this->table,$set_sql,$this->getWhereFragment());
    }
}