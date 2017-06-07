<?php
namespace zeus\database\specification;
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/6
 * Time: 21:58
 */
class QuerySpecification extends AbstractSpecification
{
    private $table;
    private $select_data = [];
    private $join_data = [];
    private $codition_data = [];
    private $having_data = [];
    private $groupby_data = [];
    private $orderby_data = [];
    private $limit_data;
    private $offset_data;

    public function __construct($is_list=true)
    {
        if($is_list){
            $dml="select-list";
        }else{
            $dml="select-one";
        }
        parent::__construct($dml);

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

    public function distinct($fields){
        //TODO
        return $this;
    }

    public function from($table){
        $this->table = trim($table);
    }

    public function join($table,$codition,$type="left join"){
        //TODO
        //return $this;
        throw new \RuntimeException("JOIN OPERATOR NOT SUPPORT");
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


}