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


    public function __get($key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        return '';
    }

    public function __set($key, $val)
    {
        //$this->config[$key] = $val;
    }

    public function __isset($name)
    {
        return isset($this->config[$name]);
    }

    public function __unset($name)
    {
        //unset($this->config[$name]);
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
        $config = include_once ZEUS_PATH . DS . "config.php";
        if(!empty($config)){
            $config = [];
        }
        if (defined("APP_ENV_PATH")) {
            if (is_file(APP_ENV_PATH)) {
                $app_config = include_once APP_ENV_PATH;
                if(!empty($app_config)){
                    $config = array_merge($config, $app_config);
                }
            }
        }
        $this->config = array_merge($this->config,$config);
    }
}