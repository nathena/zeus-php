<?php
namespace zeus\http;

use zeus\sandbox\ConfigManager;

class Cookie
{
	private static $inited = false;
	private static $instance = null;
	
	protected static $config = [
			'prefix'    => '',
			'expire'    => time(),
			'path'      => '/',
			'domain'    => '',
			'secure'    => false,
			'httponly'  => 1,
	];

	private static function init(array $config = [])
	{
		if( self::$inited ){
			return;
		}
		
		$config = ConfigManager::cookie();
		$config = array_merge(static::$config, array_change_key_case($config));
		
		ini_set('session.cookie_httponly', 1);
		
		self::$inited = true;
	}
	
	/**
	 * @return \zeus\http\Cookie
	 */
	public static function getInstance(){
		if(empty(static::$instance)){
			static::init();
			static::$instance = new static();
		}
		return static::$instance;
	}
	
	public function __get($key){
		$key = static::$config['prefix'].$key;
		if (isset($_COOKIE[$key]))
		{
			$value = $_COOKIE[$key];
			if (0 === strpos($value, 'json:'))
			{
				$value = substr($value, 5);
				$value = json_decode($value, true);
			}
			return $value;
		}
		return '';
	}
	
	public function __set($key,$val){
		$key = static::$config['prefix'].$key;
		// 设置cookie
		if (is_array($val))
		{
			$val = 'json:'.json_encode($val);
		}
		
		setcookie($key, $value, static::$config['expire'], static::$config['path'], static::$config['domain'], static::$config['secure'], static::$config['httponly']);
		$_COOKIE[$key] = $value;
		
	}

	/**
	 * 判断Cookie数据
	 * @param string        $name cookie名称
	 * @return bool
	 */
	public function has($key)
	{
		$key = static::$config['prefix'].$key;
		return !isset($_COOKIE[$key]) || empty($_COOKIE[$key]) ? false : true;
	}

	/**
	 * Cookie删除
	 * @param string        $name cookie名称
	 */
	public function delete($key)
	{
		$key = static::$config['prefix'].$key;		
		setcookie($key, '', static::$config['expire'], static::$config['path'], static::$config['domain'], static::$config['secure'], static::$config['httponly']);
		// 删除指定cookie
		unset($_COOKIE[$name]);
	}

	/**
	 * Cookie清空
	 * @param string|null $prefix cookie前缀
	 */
	public function clear()
	{
		if (empty($_COOKIE)) 
		{
			return;
		}
		
		foreach ($_COOKIE as $key => $val) 
		{
			if ($config['setcookie']) 
			{
				setcookie($key, '', static::$config['expire'], static::$config['path'], static::$config['domain'], static::$config['secure'], static::$config['httponly']);
			}
			unset($_COOKIE[$key]);
		}
	}
}