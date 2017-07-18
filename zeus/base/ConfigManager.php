<?php

namespace zeus\sandbox;

class ConfigManager implements \ArrayAccess
{
    private static $_instance;

    private $config = [];

    /**
     * @return ConfigManager
     */
    public static function getInstance(){
        if(!isset(self::$_instance)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function config($key)
    {
        $cfg = self::getInstance();
        return $cfg[$key];
    }

    public function setConfig(array $config)
    {
        if(!empty($config)){
            $this->config = array_merge($this->config,$config);
        }
    }

    public function __get($key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        return '';
    }

    public function __set($key, $val)
    {
        $this->config[$key] = $val;
    }

    public function __isset($name)
    {
        return isset($this->config[$name]);
    }

    public function __unset($name)
    {
        unset($this->config[$name]);
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

    private function __construct()
    {
        $this->config = include_once ZEUS_PATH . DS . "config.php";
    }
}