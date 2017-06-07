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

    public function setSql($sql){
        $this->sql = $sql;
    }

    public function getParams(){
        return $this->params;
    }

    public function setParams(array $params){
        $this->params = array_merge($this->params,$params);
    }

    public function getDml(){
        return $this->dml;
    }

    public function test(){
        $sql = $this->getSql();
        $param = $this->getParams();
        $indexed=$param==array_values($param);

        foreach($param as $k=>$v) {
            if(is_string($v)){
                $v="'$v'";
            }
            if($indexed){
                $sql=preg_replace('/\?/',$v,$sql,1);
            }else {
                $sql=preg_replace("/$k/",$v,$sql,1);
            }
        }
        echo "\r\n". $sql."\r\n";
    }
}