<?php
namespace zeus\database\specification;
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/6
 * Time: 21:58
 */
class SqlSpecification
{
    private $table;
    private $select = [];
    private $join = [];
    private $codition = [];
    private $having = [];
    private $groupby = [];
    private $orderby = [];
    private $limit;
    private $offset;

    private $params = [];

    public function __construct($table)
    {
        $this->table = trim($table);
    }

    public function select($fields){
        //TODO
        return $this;
    }

    public function min($fields){
        //TODO
        return $this;
    }

    public function max($fields){
        //TODO
        return $this;
    }

    public function sum($fields){
        //TODO
        return $this;
    }

    public function avg($fields){
        //TODO
        return $this;
    }

    public function distinct($fields){
        //TODO
        return $this;
    }

    public function join($table,$codition,$type="left join"){
        //TODO
        //return $this;
        throw new \RuntimeException("JOIN OPERATOR NOT SUPPORT");
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


    public function group_by($by){
        //TODO
        return $this;
    }

    public function order_by($by,$direction=''){
        //TODO
        return $this;
    }

    public function limit($limit,$offset=''){
        //TODO
        return $this;
    }

    public function offset($offset){
        //TODO
        return $this;
    }


    //-------- build
    public function fectch($params=null){
        //TODO
    }

    public function fectchAll($params=null){
        //TODO
    }

    public function insert($insert_data){
        //TODO
    }

    public function update($insert_data){
        //TODO
    }

    public function delete($insert_data){
        //TODO
    }

    //--------------

    public function getCoditionFragment(){
        //TODO
    }

    public function getSql(){
        //TODO
    }

    public function getParams(){
        //TODO
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