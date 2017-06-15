<?php

namespace zeus\base\command;

use zeus\sandbox\ApplicationContext;

abstract class AbstractCommand
{
    protected $commandId;
    protected $commandType;

    private $data = [];
    private $method;

    private $idempotent = 0;

    public function __construct()
    {
        $class = get_class($this);

        $this->commandType = $class;
        $this->commandId = $this->commandType . time();

        $class = get_class($this);
        $class_fragments = explode("\\", $class);
        $simple_class_name = end($class_fragments);

        $this->method = "handler{$simple_class_name}";
    }

    public function idempotent()
    {
        $idempotent = $this->idempotent;
        $this->idempotent = $this->idempotent+1;

        return 0 === $idempotent;
    }

    public function start()
    {
    }

    public function finished()
    {
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function execute()
    {
        ApplicationContext::currentContext()->getCommandBus()->execute($this);
    }

    public function getCommandType()
    {
        return $this->commandType;
    }

    public function getCommandId()
    {
        return $this->commandId;
    }

    public function setData($data)
    {
        if(is_array($data)){
            $this->data = array_merge($this->data, $data);
        }
    }

    public function getData()
    {
        return $this->data;
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
}