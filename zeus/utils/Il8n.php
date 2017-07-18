<?php
/**
 * User: nathena
 * Date: 2017/7/18 0018
 * Time: 13:57
 */

namespace zeus\utils;


use zeus\sandbox\ConfigManager;

class Il8n
{
    private static $_instance;

    public static function get()
    {
        return call_user_func_array(array(self::getInstance(),'getKey'),func_get_args());
    }

    public static function currentData()
    {
        return self::getInstance()->data;
    }

    private static function getInstance()
    {
        if(!isset(self::$_instance)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private $data = [];

    private function __construct()
    {
        $config = ConfigManager::config("il8n.config.path");
        if(is_file($config)){
            $this->data = parse_ini_file($config);
        }
    }

    private function getKey()
    {
        $argc = func_get_args();
        if(count($argc)>1){
            $key = array_shift($argc);
            if(isset($this->data[$key])){
                $_argc = [$this->data[$key]];
                $_argc = array_merge($_argc,$argc);
                return call_user_func_array("sprintf",$_argc);
            }
            return $key;
        }
        return '';
    }
}