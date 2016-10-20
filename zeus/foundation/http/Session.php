<?php
namespace zeus\foundation\http;

use zeus\exception\ClassNotFoundException;
use zeus\etc\ConfigManager;

class Session
{
	private static $init   = false;
	private static $start   = false;
	
	private static $config = [
			'auto_start'	=> true,
			'prefix'		=> '',
			//'use_trans_sid' => '',
			//'var_session_id'=> '',
			//'id'			=> '',
			//'name'			=> '',
			//'path'			=> '',
			//'domain'		=> '',
			'type'			=> '',
	];
	
	/**
	 * session初始化
	 * @param array $config
	 * @return void
	 * @throws \think\Exception
	 */
	public static function init(array $config = [])
	{
		if( !self::$init )
		{
			if (empty($config))
			{
				$config = ConfigManager::session();
			}
			self::$config = array_merge(self::$config, array_change_key_case($config));
			
			$config = self::$config;
			
			// 记录初始化信息
			if (isset($config['use_trans_sid']))
			{
				ini_set('session.use_trans_sid', $config['use_trans_sid'] ? 1 : 0);
			}
			if (isset($config['var_session_id']) && isset($_REQUEST[$config['var_session_id']]))
			{
				session_id($_REQUEST[$config['var_session_id']]);
			}
			elseif (isset($config['id']) && !empty($config['id']))
			{
				session_id($config['id']);
			}
			if (isset($config['name']))
			{
				session_name($config['name']);
			}
			if (isset($config['path']))
			{
				session_save_path($config['path']);
			}
			if (isset($config['domain']))
			{
				ini_set('session.cookie_domain', $config['domain']);
			}
			if (isset($config['expire']))
			{
				ini_set('session.gc_maxlifetime', $config['expire']);
				ini_set('session.cookie_lifetime', $config['expire']);
			}
			if (isset($config['use_cookies']))
			{
				ini_set('session.use_cookies', $config['use_cookies'] ? 1 : 0);
			}
			if (isset($config['cache_limiter']))
			{
				session_cache_limiter($config['cache_limiter']);
			}
			if (isset($config['cache_expire']))
			{
				session_cache_expire($config['cache_expire']);
			}
			if (!empty($config['type']))
			{
				// 读取session驱动
				$class = false !== strpos($config['type'], '\\') ? $config['type'] : '\\zeus\\http\\session\\' . ucwords($config['type']);
				// 检查驱动类
				if (!class_exists($class) || !session_set_save_handler(new $class($config)))
				{
					throw new ClassNotFoundException('error session handler:' . $class, $class);
				}
			}
			
			
			self::$init = true;
		}
		
		if( !self::$start )
		{
			if (!$config['auto_start'] && PHP_SESSION_ACTIVE != session_status())
			{
				ini_set('session.auto_start', 0);
			}
			else 
			{
				ini_set('session.auto_start', 1);
				
				session_start();
				self::$start = true;
			}
		}
	}
	
	/**
	 * 启动session
	 * @return void
	 */
	protected static function start()
	{
		self::$init || self::init();
	
		if( !self::$start )
		{
			session_start();
		}
	
		self::$start = true;
	}

	/**
	 * session设置
	 * @param string        $name session名称
	 * @param mixed         $value session值
	 * @param string|null   $prefix 作用域（前缀）
	 * @return void
	 */
	public static function set($name, $value = '', $prefix = null)
	{
		self::$start || self::start();

		$prefix = !is_null($prefix) ? $prefix : self::$config['prefix'];
		
		$_SESSION[$prefix.$name] = $value;
	}

	/**
	 * session获取
	 * @param string        $name session名称
	 * @param string|null   $prefix 作用域（前缀）
	 * @return mixed
	 */
	public static function get($name = '', $prefix = null)
	{
		self::$start || self::start();
		
		$prefix = !is_null($prefix) ? $prefix : self::$config['prefix'];
		
		return empty($name) ? $_SESSION : $_SESSION[$prefix.$name];
	}

	/**
	 * session获取并删除
	 * @param string        $name session名称
	 * @param string|null   $prefix 作用域（前缀）
	 * @return mixed
	 */
	public static function pull($name, $prefix = null)
	{
		$result = self::get($name, $prefix);
		
		if ($result) 
		{
			self::delete($name, $prefix);
		} 
		
		return $result;
	}

	/**
	 * 删除session数据
	 * @param string        $name session名称
	 * @param string|null   $prefix 作用域（前缀）
	 * @return void
	 */
	public static function delete($name, $prefix = null)
	{
		self::$start || self::start();
		
		$prefix = !is_null($prefix) ? $prefix : self::$config['prefix'];
		
		unset($_SESSION[$prefix.$name]);
	}
	
	public static function clear($prefix = null)
	{
		self::$start || self::start();
	
		$prefix = !is_null($prefix) ? $prefix : self::$config['prefix'];
	
		foreach( $_SESSION as $key => $val )
		{
			if( $prefix )
			{
				if( strpos($key, $prefix)>=0 )
				{
					unset($_SESSION[$key]);
				}
			}
			else 
			{
				unset($_SESSION[$key]);
			}
		}
	}

	/**
	 * 判断session数据
	 * @param string        $name session名称
	 * @param string|null   $prefix
	 * @return bool
	 */
	public static function has($name, $prefix = null)
	{
		$session = self::get($name);
		
		return is_null($session) || empty($session) ? false : true;
	}

	/**
	 * 销毁session
	 * @return void
	 */
	public static function destroy()
	{
		self::$start || self::start();
		
		if (!empty($_SESSION)) 
		{
			$_SESSION = [];
		}
		session_unset();
		session_destroy();
		
		self::$init = false;
		self::$start = false;
	}

	/**
	 * 重新生成session_id
	 * @param bool $delete 是否删除关联会话文件
	 * @return void
	 */
	public static function regenerate($delete = false)
	{
		self::$start || self::start();
		
		session_regenerate_id($delete);
	}

	/**
	 * 暂停session
	 * @return void
	 */
	public static function pause()
	{
		self::$start || self::start();
		
		// 暂停session
		session_write_close();
		
		self::$init = false;
		self::$start = false;
	}
}