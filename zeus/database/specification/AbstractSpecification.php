<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/7 0007
 * Time: 10:46
 */

namespace zeus\database\specification;


abstract class AbstractSpecification
{
    protected $params = [];
    protected $sql = "";

    private $dml;

    public function __construct($dml)
    {
        $this->dml = trim($dml);
    }

    public function getSql(){
        return $this->sql;
    }

    public function getParams(){
        return $this->params;
    }

    public function getDml(){
        return $this->dml;
    }


}