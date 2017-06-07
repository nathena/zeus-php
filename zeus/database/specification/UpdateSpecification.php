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
    private $fields = [];
    private $updateSqlFormat = "UPDATE `%s` set %s %s ";

    public function __construct($table,$fields)
    {
        parent::__construct();

        $this->table = $table;
        $this->dml = DmlType::DML_UPDATE;
        $this->fields = array_merge($this->fields,$fields);
    }

    public function getSql()
    {
        $this->init();

        return parent::getSql();
    }

    protected function init(){

        $params = $this->fields;

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