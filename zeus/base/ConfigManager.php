<?php
namespace zeus\sandbox;

use zeus\base\exception\ConfigNotFoundException;
use zeus\base\logger\Logger;

class ConfigManager
{
	private static $config = [];
	private static $inited = false;

	public static function config($key = '')
	{
	    if(empty($key)){
	        return static::$config;
        }

        $key = strtolower(trim($key));
	    if(isset(static::$config[$key])){
            return static::$config[$key];
        }
        return static::$config;
	}

	public static function addRouter($config){
        $routers = static::$config["router.rewrite"];
        foreach($config as $r => $w){
            if(isset($routers[$r])){
                throw new \RuntimeException(" {$r} url has already existed!");
            }

            $routers[$r] = $w;
        }
        static::$config["router.rewrite"] = $routers;
    }

    public static function init(){
        if( static::$inited ){
            return;
        }

        $config = include_once ZEUS_PATH.DS."config.php";
        static::$config = array_merge(static::$config,$config);

        if(defined("APP_ENV_PATH") ){
            $config_file = APP_ENV_PATH;
            if( is_file($config_file)){
                $config = include_once $config_file;
                static::$config = array_merge(static::$config,$config);
            }else{
                Logger::warn("Application config : {$config_file} not found");
            }
        }

        static::$inited = true;
    }

    private static function load($key)
    {
        $env_config_path = static::loadConfigFile($key);
        if( is_file($env_config_path) )
        {
            return include_once $env_config_path;
        }
        return array();
    }

    private static function loadConfigFile($key){
        $env_config_dir = defined("APP_ENV_DIR") ? APP_ENV_DIR : ZEUS_PATH;

        $env_config_path = $env_config_dir.DS.$key.".php";
        if(file_exists($env_config_path)){
            return $env_config_path;
        }

        $env_config_path = $env_config_dir.DS."config.php";
        if(file_exists($env_config_path)){
            return $env_config_path;
        }

        $env_config_path = ZEUS_PATH.DS."config.php";
        if(file_exists($env_config_path)){
            return $env_config_path;
        }

        throw new ConfigNotFoundException("Config {$key} not found in {$env_config_path}");
    }
}