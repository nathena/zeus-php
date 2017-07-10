<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/7/10
 * Time: 21:43
 */

namespace zeus\mvc;


class ModelMap implements \ArrayAccess
{
    private $code = 1;
    private $data = [];

    public function __construct()
    {

    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }


    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    public function __set($key, $val)
    {
        $this->data[$key] = $val;
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

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function getData()
    {
        return $this->data;
    }
}