<?php
namespace zeus\foundation;

defined('APP_ENV_DIR') or define('APP_ENV_DIR', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'etc');
class ConfigManager
{
	private static $config = null;
	private static $cookie = null;
	private static $session = null;
	private static $database = null;
	private static $router = null;
	
	private static function load($config)
	{
		$config = APP_ENV_DIR.DS.$config.'.php';
		if( is_file($config) )
		{
			return include_once $config;
		}
		return array();
	}
	
	public static function config($key = '')
	{
		if( is_null(self::$config))
		{
			self::$config = self::load("config");
		}
		
		if( empty($key) )
		{
			return self::$config;
		}
		
		return isset(self::$config[$key])?self::$config[$key]:"";
	}
	
	public static function cookie($key = '')
	{
		if( is_null(self::$cookie))
		{
			self::$cookie = self::load("cookie");
		}
	
		if( empty($key) )
		{
			return self::$cookie;
		}
	
		return isset(self::$cookie[$key])?self::$cookie[$key]:"";
	}
	
	public static function session($key = '')
	{
		if( is_null(self::$session))
		{
			self::$session = self::load("session");
		}
	
		if( empty($key) )
		{
			return self::$session;
		}
	
		return isset(self::$session[$key])?self::$session[$key]:"";
	}
	
	public static function database($key = '')
	{
		if( is_null(self::$database))
		{
			self::$database = self::load("database");
		}
	
		if( empty($key) )
		{
			return self::$database;
		}
	
		return isset(self::$database[$key])?self::$database[$key]:"";
	}
	
	public static function router($key = '')
	{
		if( is_null(self::$router))
		{
			self::$router = self::load("router");
		}
		
		if( empty($key) )
		{
			return self::$router;
		}
	
		return isset(self::$router[$key])?self::$router[$key]:"";
	}
}