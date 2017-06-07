<?php
namespace zeus\database\specification;
use zeus\database\DmlType;

/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/6
 * Time: 21:58
 */
class QuerySpecification extends AbstractWhereSpecification
{
    private $table;
    private $select_data = [];
    private $join_data = [];
    private $groupby_data = [];
    private $having_data = [];
    private $orderby_data = [];
    private $limit_data;
    private $offset_data;

    public function __construct($is_list=true)
    {
        parent::__construct($is_list ? DmlType::DML_SELECT_LIST : DmlType::DML_SELECT_ONE);
    }

    public function getSql()
    {
        $sql = [];
        $sql[] = implode(",",$this->select_data);
        $sql[] = $this->table;
        if(!empty($this->join_data)){
            $sql[] = implode(" ",$this->join_data);
        }
        $sql[] = $this->getWhereFragment();
        if(!empty($this->groupby_data)){
            $sql[] = "group by ".implode(",",$this->groupby_data);
        }
        if(!empty($this->having_data)){
            $having  = [];
            $having_data = $this->having_data;
            foreach($having_data as $index => $data){
                list($key,$val) = each($data);
                if(0 == $index){
                    $having[] = $val;
                }else{
                    $having[] = $key ." ".$val;
                }
            }
            if(!empty($having)){
                $sql[] = " having ".implode(",",$having);
            }
        }
        if(!empty($this->orderby_data)){
            $sql[] = " having ".implode(",",$this->orderby_data);
        }

        if( $this->offset_data>0){
            $sql[] = " limit ";
            if( $this->limit_data>0 ){
                $sql[] = "{$this->limit_data},{$this->offset_data}";
            }else{
                $sql[] = "{$this->offset_data}";
            }
        }

        return implode(" ",$sql);
    }

    public function select($fields){
        if( is_array($fields) || is_string($fields)){
            if(is_string($fields)){
                $fields = explode(",",$fields);
            }
            foreach($fields as $val){
                $val = trim($val);
                if(!empty($val)){
                    $this->select_data[] = $val;
                }
            }
        }
        return $this;
    }

    public function min($fields){
        $this->_select_agg($fields,"min");
        return $this;
    }

    public function max($fields){
        $this->_select_agg($fields,"max");
        return $this;
    }

    public function sum($fields){
        $this->_select_agg($fields,"sum");
        return $this;
    }

    public function avg($fields){
        $this->_select_agg($fields,"avg");
        return $this;
    }

    public function distinct($field){
        $this->select_data[] = "distinct {$field}";
        return $this;
    }

    public function from($table){
        $this->table = trim($table);
        return $this;
    }

    public function join($table,$codition,$type="left join"){
        $this->join_data[$table] = "{$type} {$table} on {$codition} ";
        return $this;
    }

    public function group_by($fields){
        if( is_array($fields) || is_string($fields)){
            if(is_string($fields)){
                $fields = explode(",",$fields);
            }
            foreach($fields as $val){
                $val = trim($val);
                if(!empty($val)){
                    $this->groupby_data[] = $val;
                }
            }
        }
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

    public function order_by($by,$direction=''){
        if(!empty($by)){
            $this->orderby_data[] = "{$by} {$direction}";
        }
        return $this;
    }

    public function limit($limit,$offset=''){
        if(!empty($limit)){
            $this->limit_data = $limit;
            if(!empty($offset)){
                $this->offset_data = $offset;
            }
        }
        return $this;
    }

    public function offset($offset){
        if(!empty($offset)){
            $this->offset_data = $offset;
        }
        return $this;
    }

    protected function _create_alias_from_item($item)
    {
        if (strpos($item, '.') !== FALSE)
        {
            return end(explode('.', $item));
        }

        return $item;
    }

    private function _select_agg($fields,$agg_type){
        $agg_type = strtoupper($agg_type);
        if ( !in_array($agg_type, array('MAX', 'MIN', 'AVG', 'SUM')))
        {
            return;
        }
        if( is_array($fields) || is_string($fields)){
            if(is_string($fields)){
                $fields = explode(",",$fields);
            }
            foreach($fields as $val){
                $alias = $this->_create_alias_from_item($val);
                $this->select_data[] = "{$agg_type}({$val}) as {$alias}";
            }
        }
    }

    private function _having($key,$value,$type = " and "){
        if(!empty($key) ){
            $having = [];
            $key = " having {$key} ";
            if(!$this->_has_operator($key)){
                $key .= " = ";
            }
            if( !is_null($value)){
                $named = $this->_named();
                $key .= $named;
                $this->params[$named] = $value;
            }
            $having[$type] = $key;

            $this->having_data[] = $having;
        }
    }

}