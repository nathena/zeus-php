<?php

namespace zeus\base\command;

abstract class AbstractCommand
{
    protected $commandId;
    protected $commandType;

    protected $data = [];

    public function __construct()
    {
        $class = get_class($this);

        $this->commandType = $class;
        $this->commandId = $this->commandType . time();
    }

    public function start()
    {
    }

    public function finished()
    {
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
        $this->data = array_merge($this->data,$data);
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