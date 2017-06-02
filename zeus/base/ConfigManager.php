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
        if(isset(static::$config[$key])){
            return static::$config[$key];
        }

	    $key_frames = explode(".",$key);
	    $key_ns = array_unshift($key_frames);

        $env_config_path = static::loadConfigFile($key_ns);
        if(file_exists($env_config_path)){

        }else{
            throw new ConfigNotFoundException("Config {$key} not found");
        }

        $config = static::load($env_config_path);

	}

	private static function loadConfigFile($key){
        $env_config_dir = '';
        if(defined("APP_ENV_DIR")){
            $env_config_dir = APP_ENV_DIR;
        }else{
            $env_config_dir = ZEUS_PATH;
        }

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

    private static function load($config)
    {
        if( is_file($config) )
        {
            return include_once $config;
        }
        return array();
    }
}