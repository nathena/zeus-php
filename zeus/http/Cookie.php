<?php
namespace zeus\http;


use zeus\Env;

class Cookie
{
	protected static $config = [
			// cookie 名称前缀
			'prefix'    => '',
			// cookie 保存时间
			'expire'    => 0,
			// cookie 保存路径
			'path'      => '/',
			// cookie 有效域名
			'domain'    => '',
			//  cookie 启用安全传输
			'secure'    => false,
			// 是否使用 setcookie
			'setcookie' => true,
	];

	protected static $init = false;

	/**
	 * Cookie初始化
	 * @param array $config
	 * @return void
	 */
	public static function init(array $config = [])
	{
		if( self::$init )
		{
			return;
		}
		
		if (empty($config)) 
		{
			$config = Env::cookie();
		}
		self::$config = array_merge(self::$config, array_change_key_case($config));
		ini_set('session.cookie_httponly', 1);
		
		self::$init = true;
	}

	/**
	 * Cookie 设置、获取、删除
	 *
	 * @param string $name  cookie名称
	 * @param mixed  $value cookie值
	 * @param mixed  $option 可选参数 可能会是 null|integer|string
	 *
	 * @return mixed
	 * @internal param mixed $options cookie参数
	 */
	public static function set($name, $value = '', $option = null)
	{
		self::$init || self::init();
		
		// 参数设置(会覆盖黙认设置)
		if (!is_null($option)) 
		{
			if (is_numeric($option)) 
			{
				$option = ['expire' => $option];
			} 
			elseif (is_string($option)) 
			{
				parse_str($option, $option);
			}
			$config = array_merge(self::$config, array_change_key_case($option));
		} 
		else 
		{
			$config = self::$config;
		}
		
		$name = $config['prefix'].$name;
		
		// 设置cookie
		if (is_array($value)) 
		{
			$value = 'json:'.json_encode($value);
		}
		$expire = !empty($config['expire']) ? $_SERVER['REQUEST_TIME'] + intval($config['expire']) : 0;
		
		if ($config['setcookie']) 
		{
			setcookie($name, $value, $expire, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
		}
		
		$_COOKIE[$name] = $value;
	}

	/**
	 * 判断Cookie数据
	 * @param string        $name cookie名称
	 * @return bool
	 */
	public static function has($name,$prefix=null)
	{
		$cookie = self::get($name);
		
		return is_null($cookie) || empty($cookie) ? false : true;
	}

	/**
	 * Cookie获取
	 * @param string        $name cookie名称
	 * @return mixed
	 */
	public static function get($name='',$prefix=null)
	{
		self::$init || self::init();
		
		$prefix = !is_null($prefix) ? $prefix : self::$config['prefix'];
		
		$name = $prefix.$name;
		
		if( !empty($name) )
		{
			if (isset($_COOKIE[$name]))
			{
				$value = $_COOKIE[$name];
				if (0 === strpos($value, 'json:'))
				{
					$value = substr($value, 5);
					$value = json_decode($value, true);
				}
				return $value;
			}
		}
		
		return $_COOKIE;
	}

	/**
	 * Cookie删除
	 * @param string        $name cookie名称
	 */
	public static function delete($name,$prefix=null)
	{
		self::$init || self::init();
		
		$config = self::$config;
		$prefix = !is_null($prefix) ? $prefix : $config['prefix'];
		
		$name = $prefix.$name;
		
		if ($config['setcookie']) 
		{
			setcookie($name, '', $_SERVER['REQUEST_TIME'] - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
		}
		// 删除指定cookie
		unset($_COOKIE[$name]);
	}

	/**
	 * Cookie清空
	 * @param string|null $prefix cookie前缀
	 */
	public static function clear()
	{
		if (empty($_COOKIE)) 
		{
			return;
		}
		
		$config = self::$config;
		foreach ($_COOKIE as $key => $val) 
		{
			if ($config['setcookie']) 
			{
				setcookie($key, '', $_SERVER['REQUEST_TIME'] - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
			}
			unset($_COOKIE[$key]);
		}
	}
}