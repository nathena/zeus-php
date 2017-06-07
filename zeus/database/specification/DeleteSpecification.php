<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/7 0007
 * Time: 10:43
 */

namespace zeus\database\specification;


use zeus\database\DmlType;

class DeleteSpecification extends AbstractWhereSpecification
{
    private $table;
    private $deleteSqlFormat = "DELETE FROM `%s` %s ";

    public function __construct($table)
    {
        parent::__construct(DmlType::DML_DELETE);

        $this->table = $table;
    }

    public function delete()
    {
        $this->sql = sprintf($this->deleteSqlFormat,$this->table,$this->getWhereFragment());
    }
}