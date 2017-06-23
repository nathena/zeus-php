<?php
/**
 * User: nathena
 * Date: 2017/6/23 0023
 * Time: 9:26
 */

namespace zeus\database\specification;


use zeus\database\DmlType;

class PaginationSpecification extends QuerySpecification
{
    private $_count_sql;
    public function __construct($table)
    {
        parent::__construct($table);

        $this->dml = DmlType::DML_PAGINATION;
    }

    public function getSql()
    {
        $_list_sql = parent::getSql();
        $_count_sql = $this->getCountSql();

        return [$_count_sql,$_list_sql];
    }

    protected function getCountSql()
    {
        if(empty($this->_count_sql)){
            $sql = [];
            $sql[] = "select count(*)";
            $sql[] = $this->_sql();

            $this->_count_sql = implode(" ", $sql);
        }

        return $this->_count_sql;
    }
}