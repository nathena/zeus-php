<?php
namespace zeus\domain;
use zeus\base\AbstractComponent;

/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/2
 * Time: 19:25
 */

abstract class AbstractEntity extends AbstractComponent
{
    protected $data = [];
    protected $id;
    protected $adpater;

    private $idFiled;


    public function __construct($data,$idFiled='id'){

        if(!empty($data) && is_array($data)){
            $this->data = $data;
            if(isset($data[$idFiled])){
                $this->id = trim($data[$idFiled]);
            }
        }
        $this->idFiled = $idFiled;
    }

    public function setAdpater($adpater){
        $this->adpater = $adpater;
    }
    public function getAdpater(){
        return $this->adpater;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
        $this->data[$this->idFiled] = $id;
    }

    public function getData(){
        return $this->data;
    }

    /**
     * 不允许更新id
     * @param mixed|array $data
     */
    public function setData($data){
        $this->data = array_merge($this->data,$data);
        //不允许更新id
        $this->data[$this->idFiled] = $this->id;
    }

    public function __get($key){
        if(isset($this->data[$key])){
            return $this->data[$key];
        }
        return '';
    }

    public function __set($key,$val){
        $this->data[$key] = $val;
    }
}