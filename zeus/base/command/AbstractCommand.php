<?php

namespace zeus\base\command;

use zeus\sandbox\ApplicationContext;

abstract class AbstractCommand
{
    protected $commandId;
    protected $commandType;

    private $data = [];
    private $method;

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
}