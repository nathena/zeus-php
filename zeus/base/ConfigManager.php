<?php
namespace zeus\sandbox;

use zeus\base\exception\ConfigNotFoundException;

class ConfigManager
{
	private static $config = [];

	public static function config($key = '')
	{
	    if(empty($key)){
	        return static::$config;
        }

        $key = strtolower(trim($key));
	    $key_frames = explode(".",$key);
	    $key_ns = array_unshift($key_frames);
	    if(!isset(static::$config[$key_ns])){
            static::$config[$key_ns] = static::load($key_ns);
        }

        $config = static::$config[$key_ns];
	    if(isset($config[$key])){
            return $config[$key];
        }
        throw new ConfigNotFoundException("Config {$key} not found");
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

        return '';
    }
}