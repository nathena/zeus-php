<?php

namespace zeus\domain;

use zeus\base\AbstractComponent;
use zeus\base\logger\Logger;

/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/2
 * Time: 19:25
 */
abstract class AbstractEntity extends AbstractComponent
{
    protected $data = [];
    protected $update_data = [];//更新未来提交的数据
    protected $idFiled = 'id';//uuid key
    protected $schema = "test";

    public function __construct($properties=null)
    {
        if(!empty($properties) && is_array($properties)){
            $this->setProperties($properties);
        }
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getIdFiled()
    {
        return $this->idFiled;
    }
    public function getId()
    {
        return $this->data[$this->idFiled];
    }

    public function setId($id)
    {
        $this->data[$this->idFiled] = trim($id);
    }

    public function getProperties()
    {
        return $this->data;
    }

    public function update_properties()
    {
        if(!empty($this->update_data)){
            $this->data = array_merge($this->data,$this->update_data);
            $this->update_data = [];
        }

        return $this->data;
    }

    public function getData()
    {
        return $this->update_data;
    }

    /**
     * 不允许更新id
     * 损失一点性能，提高代码复用性。见__set
     * @param mixed|array $data
     */
    public function setData($data)
    {
        if(!empty($data) && is_array($data))
        {
            foreach($data as $key => $val)
            {
                $this->{$key} = $val;
            }
        }
    }

    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return '';
    }

    public function __set($key, $val)
    {
        //不允许更新id
        if ($key == $this->idFiled || !isset($this->data[$key])) {
            return;
        }
        $this->update_data[$key] = $val;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }

    public function setProperties($data)
    {
        if(!empty($data) && is_array($data)){
            $this->data = array_merge($this->data,$data);
        }
    }
    public function setProperty($key,$val)
    {
        $this->data[$key] = $val;
    }
}