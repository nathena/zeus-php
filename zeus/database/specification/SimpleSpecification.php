<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/15
 * Time: 21:56
 */

namespace zeus\database\specification;


use zeus\database\DmlType;

class SimpleSpecification extends AbstractSpecification
{

    public function __construct($sql,$params,$dml = DmlType::DML_SELECT_LIST)
    {
        $this->setSql($sql);
        $this->setParams($params);

        $this->dml = $dml;
    }
}