<?php
namespace zeus\http\session;

use zeus\sandbox\ConfigManager;
use zeus\exception\ClassNotFoundException;

class Session
{
	private static $instance = null;
	
	/**
	 * @return \zeus\http\session\Session
	 */
	public static function getInstance()
	{
		if(empty(static::$instance)){
			static::$instance = new static();
		}
		return static::$instance;
	}
	
	private $config = [
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
	
	private $prefix = '';
	
	private function __construct()
	{
		$config = ConfigManager::session();
		$config = array_merge($this->config, array_change_key_case($config));
		
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
			$class = false !== strpos($config['type'], '\\') ? $config['type'] : __NAMESPACE__.'\\handle\\' . ucwords($config['type']);
			// 检查驱动类
			if (!class_exists($class) || !session_set_save_handler(new $class($config)))
			{
				throw new ClassNotFoundException('error session handler:' . $class, $class);
			}
		}
		
		if (!$config['auto_start'] && PHP_SESSION_ACTIVE != session_status())
		{
			ini_set('session.auto_start', 0);
		}
		else
		{
			ini_set('session.auto_start', 1);
			session_start();
		}
		
		if( isset($config['prefix'])){
			$this->prefix = $config['prefix'];
		}
		
		$this->config = $config;
	}
	
	public function __get($key){
		$key = $this->prefix.$key;
		if(isset($_SESSION[$key])){
			return $_SESSION[$key];
		}
		return '';
	}
	
	public function __set($key,$val){
		$_SESSION[$this->prefix.$key] = $val;
	}
	
	/**
	 * 删除session数据
	 * @param string        $name session名称
	 * @param string|null   $prefix 作用域（前缀）
	 * @return void
	 */
	public function delete($key)
	{
		unset($_SESSION[$this->prefix.$key]);
	}
	
	public function clear()
	{
		$prefix = $this->prefix;
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
	public function has($key)
	{
		return !isset($_SESSION[$this->prefix.$key]) || empty($_SESSION[$this->prefix.$key]) ? false : true;
	}

	/**
	 * 销毁session
	 * @return void
	 */
	public function destroy()
	{
		if (!empty($_SESSION)) 
		{
			$_SESSION = [];
		}
		session_unset();
		session_destroy();
	}

	/**
	 * 重新生成session_id
	 * @param bool $delete 是否删除关联会话文件
	 * @return void
	 */
	public function regenerate($delete = false)
	{
		session_regenerate_id($delete);
	}

	/**
	 * 暂停session
	 * @return void
	 */
	public function pause()
	{
		// 暂停session
		session_write_close();
	}
}