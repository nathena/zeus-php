<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/7 0007
 * Time: 11:47
 */

namespace zeus\database\specification;

use zeus\utils\UUIDGenerator;

class AbstractWhereSpecification extends AbstractSpecification
{
    protected $where_data=[];

    private $pre_named;
    private $pre_name_index = 0;

    public function __construct($dml)
    {
        parent::__construct($dml);
        $this->pre_named = ":_".UUIDGenerator::randChar(4)."_";
    }

    public function where($key, $value = NULL){
        $this->_where($key,$value);
        return $this;
    }

    public function or_where($key,$value=null){
        $this->_where($key,$value," or ");
        return $this;
    }

    public function where_in($key,$values){
        $this->_where_in($key,$values);
        return $this;
    }

    public function or_where_in($key,$values){
        $this->_where_in($key,$values," or ");
        return $this;
    }

    public function like($key,$match,$site="both"){
        $this->_like($key,$match,$site);
        return $this;
    }

    public function or_like($key,$match,$site="both"){
        $this->_like($key,$match,$site," or ");
        return $this;
    }

    public function getWhereFragment(){
        $where  = $this->_getWhereFragment();
        return !empty($where) ? " where {$where}" : "";
    }



    private function _where($key,$value=null,$type = 'AND ')
    {
        $where = [];
        if($key instanceof AbstractWhereSpecification )
        {
            $where[$type] = "( {$key->_getWhereFragment()} )";
            $_params = $key->getParams();
            foreach($_params as $k => $v){
                $this->params[$k] = $v;
            }
        }
        else if(is_string($key))
        {
            if(!$this->_has_operator($key)){
                $key .= " = ";
            }
            if( !is_null($value)){
                $named = $this->_named();
                $key .= $named;
                $this->params[$named] = $value;
            }
            $where[$type] = $key;
        }
        $this->where_data[] = $where;
    }

    private function _where_in($key,$values,$type = " and "){
        if(!empty($key) && !empty($values) && is_string($key) && is_array($values) )
        {
            $key.=" in ";
            $in = [];
            foreach($values as $value){
                $named = $this->_named();
                $in[] = $named;
                $this->params[$named] = $value;
            }
            if(!empty($in)){
                $where = [];
                $in = implode(", ",$in);
                $where[$type] = " $key ( $in )";

                $this->where_data[] = $where;
            }
        }
    }

    private function _like($key,$match,$side="both",$type = 'AND '){
        if(!empty($key) && !empty($match) && is_string($key) && is_string($match) && in_array($side,["both","left","right"]))
        {
            $where = [];
            $named = $this->_named();
            $key.=" like {$named}";
            switch ($side)
            {
                case 'left':
                    $match = "%{$match}";
                    break;
                case 'right':
                    $match = "{$match}%";
                    break;
                default:
                    $match = "%{$match}%";
                    break;
            }
            $this->params[$named] = $match;
            $where[$type] = " $key ";

            $this->where_data[] = $where;
        }
    }

    protected function _has_operator($str)
    {
        $str = trim($str);
        if ( ! preg_match("/(\s|<|>|!|=|is null|is not null)/i", $str))
        {
            return FALSE;
        }

        return TRUE;
    }

    protected function _named(){
        return $named = $this->pre_named.$this->pre_name_index++;
    }

    protected function _getWhereFragment(){
        $where  = [];
        $where_data = $this->where_data;
        foreach($where_data as $index => $data){
            if(empty($data)){
                continue;
            }
            list($key,$val) = each($data);
            if(0 == $index){
                $where[] = $val;
            }else{
                $where[] = $key ." ".$val;
            }
        }
        return !empty($where) ? implode(" ",$where) : "";
    }
}