<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/7 0007
 * Time: 11:47
 */

namespace zeus\database\specification;


class AbstractWhereSpecification extends AbstractSpecification
{
    protected $codition_data = [];
    protected $having_data = [];

    public function __construct($dml)
    {
        parent::__construct($dml);
    }

    public function where($key, $value = NULL){
        $this->_where($key,$value);
        return $this;
    }

    public function or_where($key,$value=null){
        $this->_where($key,$value," or ");
        return $this;
    }

    public function where_in($key,$values,$not = false){
        $this->_where_in($key,$values,$not);
        return $this;
    }

    public function or_where_in($key,$values,$not = false){
        $this->_where_in($key,$values,$not," or ");
        return $this;
    }

    public function like($key,$match,$site="both",$not=false){
        $this->_like($key,$match,$site,$not);
        return $this;
    }

    public function or_like($key,$match,$site="both",$not=false){
        $this->_like($key,$match,$site,$not," or ");
        return $this;
    }

    public function having($key,$value){
        $this->_having($key,$value);
        return $this;
    }

    public function or_having($key,$value){
        $this->_having($key,$value," or ");
        return $this;
    }

    public function getWhereFragment(){

    }

    private function _where($key,$value=null,$type = 'AND '){
        //TODO
    }

    private function _where_in($key,$values,$not= false, $type = " and "){
        //TODO
    }

    private function _like($key,$match,$side="both",$not= false, $type = 'AND '){
        //TODO
    }

    private function _having($key,$value,$type = " and "){
        //TODO
    }
}